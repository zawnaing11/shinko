<?php

namespace App\Http\Controllers\User\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Api\CartRequest;
use App\Models\Cart;
use App\Models\Store;
use App\Repositories\User\Api\CartRepository;
use App\Traits\CalcTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use CalcTrait;

    public function index(Request $request, CartRepository $cart_repository)
    {
        $result = $cart_repository->all();
        return response()->json($result);
    }

    public function store(CartRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        try {
            $result = DB::transaction(function () use ($validated, $user) {
                $store = Store::find($validated['store_id']);
                if ($store === null) {
                    throw new InvalidRequestException('店舗が存在していません。');
                }

                // ユーザーが所属する企業が店舗を管理しているかチェック
                if ($store->company_id != $user->company_id) {
                    throw new InvalidRequestException('店舗が存在していません。');
                }

                // ユーザーのカートの中身をすべて削除
                Cart::where('user_id', $user->id)->delete();

                // カートを初期化
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                ]);

                return [
                    'store_id' => $store->id,
                    'cart_id' => $cart->id,
                ];
            });

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
