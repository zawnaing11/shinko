<?php

use App\Http\Controllers\User\Api\AuthController;
use App\Http\Controllers\User\Api\CartController;
use App\Http\Controllers\User\Api\CartProductController;
use App\Http\Controllers\User\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('carts', CartController::class)->only(['index', 'store']);
    Route::apiResource('cart_products', CartProductController::class)->only(['store']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
});
