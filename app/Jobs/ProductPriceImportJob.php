<?php

namespace App\Jobs;

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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductPriceImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $row_num = 2;
    private $file_path;
    private $import;

    public function __construct($import, $file_path)
    {
        $this->import = $import;
        $this->file_path = $file_path;
    }

    public function handle()
    {
        Log::setDefaultDriver('csv_import');

        logger()->info('Line info', [
            'row_num' => $this->row_num,
            'import_id' => $this->import->id,
        ]);

        // 処理中
        $this->import->fill(['status' => 2,])->save();

        $columns = [
            'flag' => 0,
            'product_price_id' => 1,
            'store_id' => 3,
            'jan_cd' => 4,
            'price' => 7,
        ];

        try {
            // ファイル存在確認
            if (! Storage::exists($this->file_path)) {
                throw new Exception($this->import->file_name . ' が存在しません。');
            }

            $file = new \SplFileObject(storage_path('app/' . $this->file_path));
            $file->setFlags(
                \SplFileObject::READ_CSV |
                \SplFileObject::READ_AHEAD
            );

            // ヘッダー行スキップ
            if ($file->key() == 0) {
                $file->next();
            }

            while (! $file->eof()) {
                $list = [];

                for ($i = 0; $i < 1000; $i++) {
                    $line = $file->fgetcsv();
                    if (! $line) {
                        break;
                    }
                    $list[] = $line;
                }

                foreach ($list as $row) {
                    try {
                        if ($row[$columns['flag']] == 2 || $row[$columns['flag']] == 3) {
                            // 商品価格が存在するかチェック
                            $product_price = ProductPrice::find($row[$columns['product_price_id']]);
                            if (! $product_price) {
                                $this->storeImportDetail(false, ['商品価格が存在しません。']);
                                throw new Exception('商品価格が存在しません。');
                            }
                        }

                        if ($row[$columns['flag']] == 1 || $row[$columns['flag']] == 2) {

                            if ($row[$columns['flag']] == 1) {
                                // 商品が存在するかチェック
                                $base_products = BaseProduct::with([
                                    'storeBases' => function ($q) use ($row, $columns) {
                                        return $q->where('store_id', $row[$columns['store_id']]);
                                    }])
                                    ->where('jan_cd', $row[$columns['jan_cd']])
                                    ->exists();
                                if (! $base_products) {
                                    $this->storeImportDetail(false, ['商品価格が存在しません。']);
                                    throw new Exception('商品価格が存在しません。');
                                }

                                // 商品価格が存在するかチェック
                                $product_price = ProductPrice::where([
                                        ['store_id', '=', $row[$columns['store_id']]],
                                        ['jan_cd', '=', $row[$columns['jan_cd']]],
                                    ])
                                    ->exists();
                                if ($product_price) {
                                    $this->storeImportDetail(false, ['既に存在しています。']);
                                    throw new Exception('既に存在しています。');
                                }
                            }

                            $data = [
                                'store_id' => $row[$columns['store_id']],
                                'jan_cd' => $row[$columns['jan_cd']],
                                'price' => $row[$columns['price']],
                            ];
                            $this->validation($data);
                        }

                        // データ保存
                        switch ($row[$columns['flag']]) {
                            case 1:
                                $this->storeImportDetail(true);
                                $this->storeProdcutPrice($data);
                                break;
                            case 2:
                                $this->storeImportDetail(true);
                                $this->updateProductPrice($product_price, $data);
                                break;
                            case 3:
                                $this->storeImportDetail(true);
                                $this->destroyProductPrice($product_price);
                                break;
                            default:
                                $this->storeImportDetail(false, ['フラグが存在しません。']);
                                throw new Exception('フラグが存在しません。');
                        }
                    } catch (Exception $e) {
                        logger()->error('$e', [$e->getCode(), $e->getMessage()]);
                    }
                }
                // メモリ解放&初期化
                $list = null;
                gc_collect_cycles();
                unset($list);
                if ($file->eof()) {
                    break;
                }
            }
            // 完了
            $this->import->fill(['status' => 3])->save();

            Storage::delete($this->file_path);
        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            // 失敗
            $this->import->fill([
                'status' => 10,
                'messages' => [$e->getMessage()]
            ]) ->save();
        }
    }

    public function storeImportDetail(bool $result, array $messages = [])
    {
        return ImportDetail::create([
            'import_id' => $this->import->id,
            'line_number' => $this->row_num++,
            'result' => $result,
            'messages' => $messages,
        ]);
    }

    private function validation(array $data)
    {
        $product_price_request = new ProductPriceRequest();
        $validator = Validator::make($data, $product_price_request->rules(), $product_price_request->messages(), $product_price_request->attributes());

        if ($validator->fails()) {
            $this->storeImportDetail(false, $validator->messages()->all());
            throw new Exception(json_encode($validator->messages()->all()));
        }
    }

    public function storeProdcutPrice(array $data): void
    {
        $product_price = ProductPrice::create($data);
        logger()->info('Create $product_price', $product_price->toArray());
    }

    public function updateProductPrice(object $product_price, array $data): void
    {
        $product_price->fill(['price' => $data['price']])->save();
        logger()->info('Update $product_price', $product_price->toArray());
    }

    public function destroyProductPrice(object $product_price): void
    {
        $product_price->delete();
        logger()->info('Delete $product_price');
    }
}
