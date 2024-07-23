<?php

namespace App\Jobs;

use App\Exceptions\ArrayException;
use App\Exceptions\ImportException;
use App\Http\Requests\Company\UserCsvStoreRequest;
use App\Http\Requests\Company\UserCsvUpdateRequest;
use App\Models\ImportDetail;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $header_num = 1;
    private $row_num;
    private $file_path;
    private $import;
    private $cols;
    private $auth_user;

    public function __construct($import, $file_path)
    {
        $this->import = $import;
        $this->file_path = $file_path;
        $this->auth_user = auth()->user();
    }

    public function handle()
    {
        Log::setDefaultDriver('user_csv_import');

        logger()->info('Import Start', $this->import->toArray());

        $this->storeImport(2);

        $this->cols = array_flip([
            0 => 'flag',
            1 => 'id',
            2 => 'email',
            3 => 'password',
            4 => 'name',
            5 => 'retirement_date',
        ]);
        try {
            // ファイル存在確認
            if (! Storage::exists($this->file_path)) {
                throw new ImportException($this->import->file_name . ' が存在していません。');
            }

            $file = new \SplFileObject(storage_path('app/' . $this->file_path));
            $file->setFlags(
                \SplFileObject::READ_CSV |
                \SplFileObject::READ_AHEAD |
                \SplFileObject::SKIP_EMPTY |
                \SplFileObject::DROP_NEW_LINE
            );

            foreach ($file as $row) {
                $this->row_num = $file->key() + 1;
                logger()->info('row info', ['row_num' => $this->row_num]);

                // ヘッダー行スキップ
                if ($file->key() < $this->header_num) {
                    continue;
                }

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

            $this->storeImport(3);

        } catch (ImportException $ie) {
            logger()->info('$ie', [$ie->getCode(), $ie->getMessage()]);
            $this->storeImport(10, [$ie->getMessage()]);

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            $this->storeImport(10);
        }

        Storage::delete($this->file_path);

        logger()->info('Import End', $this->import->toArray());
    }

    private function storeImport(int $status, array $messages = [])
    {
        $this->import->fill([
            'status' => $status,
            'messages' => $messages
        ])
        ->save();
    }

    private function storeImportDetail(int $result, array $messages = [])
    {
        ImportDetail::create([
            'import_id' => $this->import->id,
            'line_number' => $this->row_num,
            'result' => $result,
            'messages' => $messages,
        ]);
    }

    private function validation(array $data, $user)
    {
        $user_request = $user !== null ? new UserCsvUpdateRequest() : new UserCsvStoreRequest();
        $validator = Validator::make($data, $user_request->rules(user_id: $user?->id), $user_request->messages(), $user_request->attributes());

        if ($validator->fails()) {
            throw new ArrayException($validator->messages()->all());
        }

        return $validator->validated();
    }
}
