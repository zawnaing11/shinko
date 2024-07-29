<?php

namespace App\Imports;

use App\Exceptions\ArrayException;
use App\Exceptions\ImportException;
use App\Http\Requests\Company\UserExcelStoreRequest;
use App\Http\Requests\Company\UserExcelUpdateRequest;
use App\Models\ImportDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class UserImport implements ToModel, WithStartRow, WithChunkReading
{
    use RemembersRowNumber;

    private $cols;
    private $auth_user;
    private $import_id;

    public function __construct($import_id, $auth_user)
    {
        $this->auth_user = $auth_user;
        $this->import_id = $import_id;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function model(array $row)
    {
        $this->cols = array_flip([
            0 => 'flag',
            1 => 'id',
            2 => 'email',
            3 => 'password',
            4 => 'name',
            5 => 'retirement_date',
        ]);

        logger()->info('row info', ['row_num' => $this->getRowNumber()]);

        try {
            DB::transaction(function () use ($row) {

                $id = $row[$this->cols['id']];

                $user = User::find($id);

                if ($user !== null && $user->company_id !== $this->auth_user->company_id) {
                    throw new ImportException('ユーザーが存在していません。');
                }

                if ($row[$this->cols['flag']] == 1) {
                    // ユーザーが存在するかチェック
                    if ($user === null) {
                        throw new ImportException('ユーザーが存在していません。');
                    }

                    $user->delete();

                } else {
                    $data = [
                        'email' => $row[$this->cols['email']],
                        'name' => $row[$this->cols['name']],
                        'password' => $row[$this->cols['password']],
                        'retirement_date' => $row[$this->cols['retirement_date']] == '' ? null : $row[$this->cols['retirement_date']],
                    ];

                    $validated = $this->validation($data, $user);
                    $validated['company_id'] = $this->auth_user->company_id;

                    User::updateOrCreate(
                        [
                            'id' => $id,
                        ],
                        $validated
                    );
                }

                $this->storeImportDetail(1);

            });

        } catch (ImportException $ie) {
            logger()->info('$ie', [$ie->getCode(), $ie->getMessage()]);
            $this->storeImportDetail(10, [$ie->getMessage()]);

        } catch (ArrayException $ae) {
            logger()->info('$ae', [$ae->getCode(), $ae->getMessages()]);
            $this->storeImportDetail(10, $ae->getMessages());

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            $this->storeImportDetail(10, [$e->getMessage()]);
        }
    }

    private function storeImportDetail(int $result, array $messages = [])
    {
        ImportDetail::create([
            'import_id' => $this->import_id,
            'line_number' => $this->getRowNumber(),
            'result' => $result,
            'messages' => $messages,
        ]);
    }

    private function validation(array $data, $user)
    {
        $user_request = $user !== null ? new UserExcelUpdateRequest() : new UserExcelStoreRequest();
        $validator = Validator::make($data, $user_request->rules(user_id: $user?->id, email: $data['email']), $user_request->messages(), $user_request->attributes());

        if ($validator->fails()) {
            throw new ArrayException($validator->messages()->all());
        }

        return $validator->validated();
    }
}
