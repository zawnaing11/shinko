<?php

namespace App\Repositories\User\Api;

use App\Http\Resources\User\CartProductResource;
use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    public function all()
    {
        $shinko_db_name = DB::connection('mysql_shinko')->getDatabaseName();
        $carts = Cart::where('user_id', Auth::user()->id)
            ->join('cart_products', 'cart_products.cart_id', '=', 'carts.id')
            ->join($shinko_db_name . '.store_bases', 'store_bases.store_id', '=', 'carts.store_id')
            ->join($shinko_db_name . '.base_products', function ($join) {
                $now = Carbon::now();
                $join->on('base_products.base_id', '=', 'store_bases.base_id')
                    ->on('base_products.jan_cd', '=', 'cart_products.jan_cd')
                    ->where([
                        ['price_start_date', '<=', $now],
                        ['price_end_date', '>=', $now],
                    ]);
            })
            ->join($shinko_db_name . '.ms_products', 'ms_products.jan_cd', '=', 'base_products.jan_cd')
            ->leftJoin('product_prices', function ($join) {
                $join->on('product_prices.jan_cd', '=', 'cart_products.jan_cd')
                    ->on('product_prices.store_id', '=', 'carts.store_id');
            })
            ->select(
                'carts.id as cart_id',
                'cart_products.jan_cd as jan_cd',
                'cart_products.quantity as quantity',
                'product_prices.price_tax as price_tax',
                'base_products.list_price as list_price',
                'base_products.list_price_tax as list_price_tax',
                'ms_products.tax_rate as tax_rate'
            )
            ->orderBy('cart_products.updated_at', 'DESC')
            ->get();

        $result = [];
        foreach ($carts as $cart) {
            if (! array_key_exists($cart->cart_id, $result)) {
                $result[$cart->cart_id] = [
                    'cart_id' => $cart->cart_id,
                    'products' => [],
                ];
            }
            $result[$cart->cart_id]['products'][] = new CartProductResource($cart);
        }

        return $result;
    }
}
