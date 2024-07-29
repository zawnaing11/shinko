<?php

namespace App\Imports;

use App\Exceptions\ArrayException;
use App\Exceptions\ImportException;
use App\Http\Requests\Company\ProductPriceRequest;
use App\Models\BaseProduct;
use App\Models\CompanyAdminUserStore;
use App\Models\ImportDetail;
use App\Models\ProductPrice;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductPriceImport implements ToModel, WithStartRow, WithChunkReading
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
            0 => 'store_id',
            2 => 'jan_cd',
            5 => 'price_tax',
        ]);

        logger()->info('row info', ['row_num' => $this->getRowNumber()]);

        try {
            DB::transaction(function () use ($row) {

                $store_id = $row[$this->cols['store_id']];
                $jan_cd = $row[$this->cols['jan_cd']];

                // 自分の店舗かチェック
                $company_store = CompanyAdminUserStore::where([
                    'company_admin_user_id' => $this->auth_user->id,
                    'store_id' => $store_id,
                ])->first();
                if ($company_store === null) {
                    throw new ImportException('店舗が存在していません。');
                }

                // 商品が存在するかチェック
                $base_products = BaseProduct::where('jan_cd', $jan_cd)
                    ->current()
                    ->whereHas('storeBases', function ($q) use ($store_id) {
                        $q->where('store_id', $store_id);
                    })
                    ->exists();
                if (! $base_products) {
                    throw new ImportException('商品が存在していません。');
                }

                $validated = $this->validation(['price_tax' => $row[$this->cols['price_tax']]]);
                ProductPrice::updateOrCreate(
                    [
                        'store_id' => $store_id,
                        'jan_cd' => $jan_cd,
                    ],
                    [
                        'price_tax' => $validated['price_tax'],
                    ]
                );

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
        return ImportDetail::create([
            'import_id' => $this->import_id,
            'line_number' => $this->getRowNumber(),
            'result' => $result,
            'messages' => $messages,
        ]);
    }

    private function validation(array $data)
    {
        $product_price_request = new ProductPriceRequest();
        $validator = Validator::make($data, $product_price_request->rules(), $product_price_request->messages(), $product_price_request->attributes());

        if ($validator->fails()) {
            throw new ArrayException($validator->messages()->all());
        }

        return $validator->validated();
    }
}
