<?php

use App\Http\Controllers\User\Api\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('stores', StoreController::class)->only(['show']);
