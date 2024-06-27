<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\BaseProduct;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $base_products = BaseProduct::join('store_bases', 'store_bases.base_id', '=', 'base_products.base_id')
            ->join('store', 'store.id', '=', 'store_bases.store_id')
            ->join('company_admin_user_stores', 'company_admin_user_stores.store_id', '=', 'store.id')
            ->leftJoin('product_prices', function($join) {
                $join->on('product_prices.jan_cd','=','base_products.jan_cd');
                $join->on('product_prices.store_id', '=', 'store.id');
            })
            ->where('company_admin_user_stores.company_admin_user_id', Auth::user()->id)
            ->where('base_products.price_end_date', '>=', Carbon::now())
            ->select('product_prices.id as product_price_id', 'store.id as store_id', 'store.name as store_name', 'base_products.jan_cd', 'base_products.wholesale_price', 'product_prices.price');

        if ($request->filled('store_name')) {
            $base_products->where('store.name', 'like', '%' . $request->store_name . '%');
        }
        if ($request->filled('jan_cd')) {
            $base_products->where('base_products.jan_cd', 'like', '%' . $request->jan_cd . '%');
        }
        if ($request->filled('wholesale_price')) {
            $base_products->where('base_products.wholesale_price', $request->wholesale_price);
        }
        if ($request->filled('price')) {
            $base_products->where('product_prices.price', $request->price);
        }

        $base_products = $base_products->paginate(100);

        return view('company.product_prices.index', [
            'product_prices' => $base_products
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $store_id, string $jan_cd)
    {
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
    public function update(Request $request)
    {
        ProductPrice::updateOrCreate(
            [
                'store_id' => $request->store_id,
                'jan_cd' => $request->jan_cd,
            ],
            ['price' => $request->price]
        );

        return redirect()->route('company.product_prices.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPrice $product_price)
    {
        $product_price->delete();

        return back()->with('alert.success', 'プレゼントコードを削除しました。');
    }
}
