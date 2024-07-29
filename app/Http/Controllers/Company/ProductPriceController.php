<?php

namespace App\Http\Controllers\Company;

use App\Exports\ProductPriceExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\ProductPriceRequest;
use App\Http\Requests\ExcelUploadRequest;
use App\Jobs\ExcelImportJob;
use App\Models\BaseProduct;
use App\Models\Import;
use App\Models\ProductPrice;
use App\Models\Store;
use App\Repositories\Company\ProductPriceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ProductPriceRepository $product_price_repository)
    {
        $base_products = $product_price_repository->all()
            ->where('company_admin_user_stores.company_admin_user_id', auth()->user()->id);

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
                    $q->where('company_admin_user_id', auth()->user()->id);
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

    public function export()
    {
        $file_name = '商品価格_' . Carbon::now()->format('YmdHis') . '.xlsx';
        return Excel::download(new ProductPriceExport(), $file_name);
    }

    public function upload(ExcelUploadRequest $request)
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
                $file_path = Storage::putFileAs(config('const.imports.excel_file_path') . 'product_prices', $import_file, $new_file_name);

                dispatch(new ExcelImportJob($import, $file_path))
                    ->onQueue('excel_import');
            });
        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            return back()
                ->with('alert.error', 'Excelアップロードに失敗しました。')
                ->withInput();
        }

        return redirect()
            ->back()
            ->with('alert.success', 'Excelアップロード受け付けました。');
    }
}
