<?php

namespace App\Http\Controllers\User\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Api\LoginRequest;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken($user->id)->plainTextToken;
            return response()->json(['token' => $token]);
        }
        return response()->json(['error_messages' => ['Eメールアドレスまたはパスワードに誤りがあります。']], 400);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        // ユーザーのカートの中身をすべて削除
        Cart::where('user_id', $user->id)->delete();
        $user->currentAccessToken()->delete();
    }
}