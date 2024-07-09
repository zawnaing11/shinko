<?php

namespace App\Repositories\Company;

use App\Models\BaseProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPriceRepository
{
    public function all()
    {
        return BaseProduct::join('ms_products', 'ms_products.jan_cd', '=', 'base_products.jan_cd')
            ->join('store_bases', 'store_bases.base_id', '=', 'base_products.base_id')
            ->join('store', 'store.id', '=', 'store_bases.store_id')
            ->join('company_admin_user_stores', 'company_admin_user_stores.store_id', '=', 'store.id')
            ->leftJoin(DB::connection()->getDatabaseName() . '.product_prices', function ($join) {
                $join->on('product_prices.jan_cd', '=', 'base_products.jan_cd')
                    ->on('product_prices.store_id', '=', 'store.id');
            })
            ->current() // TODO base_productsにマッチしないが、product_pricesに存在する場合、どのように扱うか？
            ->where('company_admin_user_stores.company_admin_user_id', Auth::user()->id)
            ->select(
                'product_prices.id as product_price_id',
                'store.id as store_id',
                'store.name as store_name',
                'base_products.jan_cd',
                'ms_products.product_name',
                'base_products.list_price',
                'product_prices.price'
            );
    }
}
