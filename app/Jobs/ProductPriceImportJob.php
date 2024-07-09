<?php

namespace App\Jobs;

use App\Exceptions\ArrayException;
use App\Exceptions\InvalidDataException;
use App\Http\Requests\Company\ProductPriceRequest;
use App\Models\BaseProduct;
use App\Models\ImportDetail;
use App\Models\ProductPrice;
use Carbon\Carbon;
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
        $this->import->fill(['status' => 2])->save();

        $cols = array_flip([
            0 => 'flag',
            1 => 'store_id',
            3 => 'jan_cd',
            6 => 'price',
        ]);

        try {
            // ファイル存在確認
            if (! Storage::exists($this->file_path)) {
                throw new InvalidDataException($this->import->file_name . ' が存在しません。');
            }

            $file = new \SplFileObject(storage_path('app/' . $this->file_path));
            $file->setFlags(
                \SplFileObject::READ_CSV |
                \SplFileObject::READ_AHEAD
            );

            foreach ($file as $row) {
                // ヘッダー行スキップ
                if (($file->key() + 1) < $this->row_num) {
                    continue;
                }
                try {
                    // for empty and new line row
                    if (count($row) == 1) {
                        throw new InvalidDataException('フラグが存在しません。');
                    }

                    // 商品が存在するかチェック
                    $base_products = BaseProduct::with([
                        'storeBases' => function ($q) use ($row, $cols) {
                            return $q->where('store_id', $row[$cols['store_id']]);
                        }])
                        ->where('jan_cd', $row[$cols['jan_cd']])
                        ->exists();
                    if (! $base_products) {
                        throw new InvalidDataException('商品が存在しません。');
                    }

                    if ($row[$cols['flag']] == 1) {
                        // 商品価格が存在するかチェック
                        $product_price = ProductPrice::where([
                                ['store_id', '=', $row[$cols['store_id']]],
                                ['jan_cd', '=', $row[$cols['jan_cd']]],
                            ])
                            ->first();
                        if ($product_price === null) {
                            throw new InvalidDataException('商品価格が存在しません。');
                        }

                        $product_price->delete();
                        logger()->info('Delete $product_price');
                    } else {
                        $this->validation(['price' => $row[$cols['price']]]);

                        $product_price = ProductPrice::updateOrCreate(
                            [
                                'store_id' => $row[$cols['store_id']],
                                'jan_cd' => $row[$cols['jan_cd']],
                            ],
                            [
                                'price' => $row[$cols['price']],
                            ]
                        );
                        logger()->info('Update $product_price');
                    }

                    $this->storeImportDetail(1);

                } catch (InvalidDataException $e) {
                    logger()->error('$e', [$e->getCode(), $e->getMessage()]);
                    $this->storeImportDetail(10, [$e->getMessage()]);
                } catch (ArrayException $ae) {
                    logger()->error('$e', [$ae->getCode(), $ae->getMessages()]);
                    $this->storeImportDetail(10, $ae->getMessages());
                }
            }
            // 完了
            $this->import->fill(['status' => 3])->save();

            Storage::delete($this->file_path);

        } catch (InvalidDataException $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            // 失敗
            $this->import->fill([
                'status' => 10,
                'messages' => [$e->getMessage()]
            ]) ->save();
        }
    }

    private function storeImportDetail(int $result, array $messages = [])
    {
        return ImportDetail::create([
            'import_id' => $this->import->id,
            'line_number' => $this->row_num++,
            'result' => $result,
            'messages' => $messages,
            'created_at' => Carbon::now(),
        ]);
    }

    private function validation(array $data)
    {
        $product_price_request = new ProductPriceRequest();
        $validator = Validator::make($data, $product_price_request->rules(), $product_price_request->messages(), $product_price_request->attributes());

        if ($validator->fails()) {
            throw new ArrayException($validator->messages()->all());
        }
    }
}
