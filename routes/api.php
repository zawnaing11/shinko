<?php

use App\Http\Controllers\User\Api\AuthController;
use App\Http\Controllers\User\Api\CartController;
use App\Http\Controllers\User\Api\StoreController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('stores', StoreController::class)->only(['show']);
    Route::apiResource('carts', CartController::class)->only(['store', 'update', 'destroy']);
});
