<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\ProductPriceRequest;
use App\Http\Requests\CsvUploadRequest;
use App\Jobs\ProductPriceImportJob;
use App\Models\BaseProduct;
use App\Models\Import;
use App\Models\ProductPrice;
use App\Models\Store;
use App\Repositories\Company\ProductPriceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ProductPriceRepository $product_price_repository)
    {
        $base_products = $product_price_repository->all()
            ->where('company_admin_user_stores.company_admin_user_id', Auth::user()->id);

        if ($request->filled('store_name')) {
            $base_products->where('store.id', $request->store_name);
        }
        if ($request->filled('jan_cd')) {
            $base_products->where('base_products.jan_cd', 'like', '%' . $request->jan_cd . '%');
        }
        if ($request->filled('product_name')) {
            $base_products->where('ms_products.product_name', 'like', '%' . $request->product_name . '%');
        }

        $base_products = $base_products->paginate(config('const.product_prices.default_paginate_number'));

        return view('company.product_prices.index', [
            'product_prices' => $base_products,
            'stores' => Store::whereHas('companyAdminUserStore', function ($q) {
                    $q->where('company_admin_user_id', Auth::user()->id);
                })->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $store_id, string $jan_cd, ProductPriceRepository $product_price_repository)
    {
        $base_product = $product_price_repository->all()
            ->where([
                ['store.id', $store_id],
                ['base_products.jan_cd', $jan_cd]
            ])
            ->first();

        // 商品が存在するかチェック
        if ($base_product === null) {
            abort(400);
        }

        return view('company.product_prices.edit', [
            'product_price' => $base_product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $store_id, string $jan_cd, ProductPriceRequest $request)
    {
        // 商品が存在するかチェック
        $base_products = BaseProduct::where('jan_cd', $jan_cd)
            ->current()
            ->whereHas('storeBases', function ($q) use ($store_id) {
                $q->where('store_id', $store_id);
            });
        if ($base_products->doesntExist()) {
            abort(400);
        }

        ProductPrice::updateOrCreate(
            [
                'store_id' => $store_id,
                'jan_cd' => $jan_cd,
            ],
            $request->validated()
        );

        return redirect()
            ->route('company.product_prices.index')
            ->with('alert.success', '商品価格の作成に成功しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPrice $product_price)
    {
        $product_price->delete();
        return back()->with('alert.success', '商品価格を削除しました。');
    }

    public function export(ProductPriceRepository $product_price_repository)
    {
        $headers = [
            'Content-Type' => 'application/octet-stream',
        ];

        $file_name = '商品価格_' . Carbon::now()->format('YmdHis') . '.csv';

        $callback = function () use ($product_price_repository) {

            $handle = fopen('php://output', 'w');

            // 文字コードをShift-JISに変換
            stream_filter_prepend($handle, 'convert.iconv.utf-8/cp932//TRANSLIT');

            fputcsv($handle, [
                '削除（1=削除）',
                '店舗ID',
                '店舗名',
                'JANコード',
                '商品名',
                '定価価格（税抜）',
                '販売価格（税抜）',
            ]);

            $product_price_repository->all()
                ->where('company_admin_user_stores.company_admin_user_id', Auth::user()->id)
                ->orderBy('store_id', 'ASC')
                ->orderBy('base_products.jan_cd', 'ASC')
                ->chunk(1000, function ($base_products) use ($handle) {
                    foreach ($base_products as $base_product) {
                        $values = [
                            'flag' => '',
                            'store_id' => $base_product->store_id,
                            'store_name' => $base_product->store_name,
                            'jan_cd' => $base_product->jan_cd,
                            'product_name' => str_replace("\x1F", '', $base_product->product_name), // remove unit separator in product_name
                            'list_price' => $base_product->list_price,
                            'price' => $base_product->price,
                        ];
                        fputcsv($handle, $values);
                    }
                });
            fclose($handle);
        };

        return response()->streamDownload($callback, $file_name, $headers);
    }

    public function upload(CsvUploadRequest $request)
    {
        $import_file = $request->validated()['import_file'];

        try {
            DB::transaction(function () use ($import_file) {
                $import = Import::create([
                    'model_name' => 'ProductPrice',
                    'file_name' => $import_file->getClientOriginalName(),
                    'status' => 1,
                ]);

                $new_file_name = uniqid() . '.' . $import_file->getClientOriginalExtension();
                $file_path = Storage::putFileAs(config('const.imports.file_path'), $import_file, $new_file_name);

                dispatch(new ProductPriceImportJob($import, $file_path))
                    ->onQueue('import');
            });
        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            return back()
                ->with('alert.error', 'CSVアップロードに失敗しました。')
                ->withInput();
        }

        return redirect()
            ->back()
            ->with('alert.success', 'CSVアップロード受け付けました。');
    }
}
