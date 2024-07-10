<?php

namespace App\Http\Controllers\User\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Api\CartProductRequest;
use App\Models\BaseProduct;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Repositories\User\Api\CartRepository;
use Exception;

class CartProductController extends Controller
{
    public function store(CartProductRequest $request, Cart $cart)
    {
        $validated = $request->validated();

        try {
            // ユーザーのカートかチェック
            if ($request->user()->id != $cart->user_id) {
                throw new Exception('Not Found.', 404);
            }

            // JANコードが存在するかチェック
            $base_product = BaseProduct::where('jan_cd', $validated['jan_cd'])
                ->current()
                ->whereHas('storeBases', function ($q) use ($cart) {
                    $q->where('store_id', $cart->store_id);
                });
            if ($base_product->doesntExist()) {
                throw new InvalidRequestException('商品が存在していません。');
            }

            if (empty($validated['quantity'])) {
                CartProduct::where([
                    'cart_id' => $cart->id,
                    'jan_cd' => $validated['jan_cd'],
                ])->delete();

            } else {
                $result = CartProduct::updateOrCreate([
                    'cart_id' => $cart->id,
                    'jan_cd' => $validated['jan_cd'],
                ], $validated);
            }

            // 現在のカートの中身を返却
            $cart_repository = new CartRepository();
            $result = $cart_repository->all();

        } catch (InvalidRequestException $ire) {
            logger()->info('$ire', [$ire->getCode(), $ire->getMessage()]);
            return response()->json(['message' => $ire->getMessage()], 400);

        } catch (Exception $e) {
            logger()->error('$e', [$e->getCode(), $e->getMessage()]);
            abort($e->getCode(), $e->getMessage());
        }

        return response()->json($result);
    }

}
