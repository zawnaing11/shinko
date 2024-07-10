<?php

use App\Http\Controllers\User\Api\AuthController;
use App\Http\Controllers\User\Api\CartController;
use App\Http\Controllers\User\Api\CartProductController;
use App\Http\Controllers\User\Api\NotificationController;
use App\Http\Controllers\User\Api\OrderController;
use App\Http\Controllers\User\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('carts', CartController::class)->only(['index', 'store']);
    Route::apiResource('carts.cart_products', CartProductController::class)->only(['store']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
    Route::apiResource('users', UserController::class)->only(['update']);
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'show']);
});