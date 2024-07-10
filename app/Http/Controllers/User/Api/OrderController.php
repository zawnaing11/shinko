<?php

namespace App\Http\Controllers\User\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Api\OrderRequest;
use App\Models\BaseProduct;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Traits\TaxTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use TaxTrait;

    public function index(Request $request)
    {
        $orders = Order::with(['products'])
            ->where('user_id', $request->user()->id)
            ->get();

        // TODO API Resource
        return response()->json($orders);
    }

    public function show(Order $order)
    {
        return response()->json($order->load('products'));
    }

    public function store(OrderRequest $request)
    {
        $validated = $request->validated();

        try {
            $cart = Cart::with(['products', 'store'])
                ->where('id', $validated['cart_id'])
                ->first();
            if ($cart === null) {
                throw new InvalidRequestException('カートが存在していません。');
            }

            // カート内商品の価格取得
            $base_products = BaseProduct::whereIn('base_products.jan_cd', $cart->products()->pluck('jan_cd'))
                ->current()
                ->join('ms_products', 'ms_products.jan_cd', '=', 'base_products.jan_cd')
                ->join('store_bases', function ($join) use ($cart) {
                    $join->on('store_bases.base_id', '=', 'base_products.base_id')
                        ->where('store_bases.store_id', $cart->store_id);
                })
                ->leftJoin(DB::connection()->getDatabaseName() . '.product_prices', function ($join) {
                    $join->on('product_prices.jan_cd', '=', 'base_products.jan_cd')
                        ->on('product_prices.store_id', '=', 'store_bases.store_id');
                })
                ->select(
                    'base_products.jan_cd as jan_cd',
                    'product_prices.price as price',
                    'base_products.list_price as list_price',
                    'ms_products.tax_rate as tax_rate',
                    'ms_products.product_name as product_name'
                )
                ->get();

            if ($cart->products()->count() != $base_products->count()) {
                throw new InvalidRequestException('既に存在しない商品がカートに含まれています。');
            }

            $user = $request->user();
            DB::transaction(function () use ($cart, $base_products, $user) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'store_id' => $cart->store_id,
                    'user_name' => $user->name,
                    'store_name' => $cart->store->name,
                ]);

                // insert()でも可能だが$fillableチェックが入らないためcreate()で実装
                foreach ($cart->products as $product) {
                    $base_product = $base_products->where('jan_cd', $product->jan_cd)->first();
                    OrderProduct::create([
                        'order_id' => $order->id,
                        'jan_cd' => $product->jan_cd,
                        'quantity' => $product->quantity,
                        'product_name' => $base_product->product_name,
                        'price' => $base_product->price,
                        'price_tax' => $this->calcTax($base_product->price, $base_product->tax_rate),
                        'list_price' => $base_product->list_price,
                        'list_price_tax' => $this->calcTax($base_product->list_price, $base_product->tax_rate),
                    ]);
                }

                $cart->delete();

                // TODO 決済
            });

        } catch (InvalidRequestException $ire) {
            logger()->info('$ire', [$ire->getCode(), $ire->getMessage()]);
            return response()->json(['message' => $ire->getMessage()], 400);

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            abort($e->getCode(), $e->getMessage());
        }

        return response()->json([], 201);
    }

}
