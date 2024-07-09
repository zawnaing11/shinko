<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\AuthController;
use App\Http\Controllers\Company\ImportController;
use App\Http\Controllers\Company\ImportDetailController;
use App\Http\Controllers\Company\ProductPriceController;
use App\Http\Controllers\Company\UserController;

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
    ->controller(AuthController::class)
    ->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('authenticate', 'authenticate')->name('authenticate');
    });

Route::middleware(['auth:company', 'is_active:company'])
    ->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::view('index', 'company.index')->name('index');
        Route::get('/', function () {
            return redirect()->route('company.product_prices.index');
        });
        // 商品価格管理
        Route::prefix('product_prices')->name('product_prices.')->controller(ProductPriceController::class)->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('{store_id}/{jan_cd}/edit', 'edit')->name('edit');
            Route::put('{store_id}/{jan_cd}', 'update')->name('update');
            Route::delete('{product_price?}', 'destroy')->name('destroy');
            Route::get('export', 'export')->name('export');
            Route::post('upload', 'upload')->name('upload');
        });
        // ユーザー管理
        Route::resource('users', UserController::class)->except('show');
        // インポート
        Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
        Route::get('imports/{import}/details', [ImportDetailController::class, 'index'])->name('imports.show');
    });
