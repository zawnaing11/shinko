<?php

namespace App\Jobs;

use App\Exceptions\ArrayException;
use App\Exceptions\ImportException;
use App\Http\Requests\Company\ProductPriceRequest;
use App\Models\BaseProduct;
use App\Models\ImportDetail;
use App\Models\ProductPrice;
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

class ProductPriceImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $header_num = 1;
    private $row_num;
    private $file_path;
    private $import;
    private $cols;

    public function __construct($import, $file_path)
    {
        $this->import = $import;
        $this->file_path = $file_path;
    }

    public function handle()
    {
        Log::setDefaultDriver('csv_import');

        logger()->info('Import Start', $this->import->toArray());

        $this->storeImport(2);

        $this->cols = array_flip([
            0 => 'flag',
            1 => 'store_id',
            3 => 'jan_cd',
            6 => 'price',
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

                        $store_id = $row[$this->cols['store_id']];
                        $jan_cd = $row[$this->cols['jan_cd']];

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

                        if ($row[$this->cols['flag']] == 1) {
                            // 商品価格が存在するかチェック
                            $product_price = ProductPrice::where([
                                    ['store_id', '=', $store_id],
                                    ['jan_cd', '=', $jan_cd],
                                ])
                                ->first();
                            if ($product_price === null) {
                                throw new ImportException('商品価格が存在していません。');
                            }

                            $product_price->delete();

                        } else {
                            $validated = $this->validation(['price' => $row[$this->cols['price']]]);
                            ProductPrice::updateOrCreate(
                                [
                                    'store_id' => $store_id,
                                    'jan_cd' => $jan_cd,
                                ],
                                [
                                    'price' => $validated['price'],
                                ]
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
                    Storage::delete($this->file_path);
                    $this->storeImportDetail(10, [$e->getMessage()]);
                    $this->storeImport(10);
                }
            }

            $this->storeImport(3);
            Storage::delete($this->file_path);

        } catch (ImportException $ie) {
            logger()->info('$ie', [$ie->getCode(), $ie->getMessage()]);
            $this->storeImport(10, [$ie->getMessage()]);

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            Storage::delete($this->file_path);
            $this->storeImport(10);
        }

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
        return ImportDetail::create([
            'import_id' => $this->import->id,
            'line_number' => $this->row_num,
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
