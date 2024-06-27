<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\Auth\LoginController;
use App\Http\Controllers\Company\ProductPriceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest:company')
    ->controller(LoginController::class)
    ->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('authenticate', 'authenticate')->name('authenticate');
    });

Route::middleware(['auth:company', 'is_active:company'])
    ->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::view('index', 'company.index')->name('index');
        Route::get('/', function () {
            return redirect()->route('company.index');
        });
        // 商品価格管理
        Route::get('product_prices', [ProductPriceController::class, 'index'])->name('product_prices.index');
        Route::get('product_prices/{store_id}/{jan_cd}/edit', [ProductPriceController::class, 'edit'])->name('product_prices.edit');
        Route::put('product_prices/{product_price?}', [ProductPriceController::class, 'update'])->name('product_prices.update');
        Route::delete('product_prices/{product_price?}', [ProductPriceController::class, 'destroy'])->name('product_prices.destroy');
    });
