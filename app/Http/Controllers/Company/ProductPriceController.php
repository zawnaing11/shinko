<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\ProductPriceRequest;
use App\Models\BaseProduct;
use App\Models\ProductPrice;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $base_products = BaseProduct::join('ms_products', 'ms_products.jan_cd', '=', 'base_products.jan_cd')
            ->join('store_bases', 'store_bases.base_id', '=', 'base_products.base_id')
            ->join('store', 'store.id', '=', 'store_bases.store_id')
            ->join('company_admin_user_stores', 'company_admin_user_stores.store_id', '=', 'store.id')
            ->leftJoin(DB::connection()->getDatabaseName() . '.product_prices', function ($join) {
                $join->on('product_prices.jan_cd', '=', 'base_products.jan_cd')
                    ->on('product_prices.store_id', '=', 'store.id');
            })
            ->where('company_admin_user_stores.company_admin_user_id', Auth::user()->id)
            ->where([ // TODO base_productsにマッチしないが、product_pricesに存在する場合、どのように扱うか？
                ['base_products.price_start_date', '<=', Carbon::now()],
                ['base_products.price_end_date', '>=', Carbon::now()],
            ])
            ->select(
                'product_prices.id as product_price_id',
                'store.id as store_id',
                'store.name as store_name',
                'base_products.jan_cd',
                'ms_products.product_name',
                'base_products.list_price',
                'product_prices.price'
            );

        if ($request->filled('store_name')) {
            $base_products->where('store.id', $request->store_name);
        }
        if ($request->filled('jan_cd')) {
            $base_products->where('base_products.jan_cd', 'like', '%' . $request->jan_cd . '%');
        }
        if ($request->filled('product_name')) {
            $base_products->where('ms_products.product_name', 'like', '%' . $request->product_name . '%');
        }

        $base_products = $base_products->paginate(config('const.default_company_paginate_number'));

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
    public function edit(int $store_id, string $jan_cd)
    {
        // 商品が存在するかチェック
        $base_products = BaseProduct::with(['storeBases' => function ($q) use ($store_id) {
            return $q->where('store_id', $store_id);
        }])->where('jan_cd', $jan_cd);
        if ($base_products->doesntExist()) {
            abort(400);
        }

        $product_price = ProductPrice::where([
                ['store_id', $store_id],
                ['jan_cd', $jan_cd],
            ])
            ->first();

        return view('company.product_prices.edit', [
            'product_price' => $product_price,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductPriceRequest $request, int $store_id, string $jan_cd)
    {
        // 商品が存在するかチェック
        $base_products = BaseProduct::with(['storeBases' => function ($q) use ($store_id) {
            return $q->where('store_id', $store_id);
        }])->where('jan_cd', $jan_cd);
        if ($base_products->doesntExist()) {
            abort(400);
        }

        $validated = $request->validated();
        logger()->info('$validated', $validated);

        ProductPrice::updateOrCreate(
            [
                'store_id' => $store_id,
                'jan_cd' => $jan_cd,
            ],
            $validated
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
}
