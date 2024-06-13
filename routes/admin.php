<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;

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

Route::middleware('guest:admin')
    ->controller(LoginController::class)
    ->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('authenticate', 'authenticate')->name('authenticate');
    });

Route::middleware(['auth:admin', 'is_active'])
    ->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::view('index', 'admin.index')->name('index');
        Route::get('/', function () {
            return redirect()->route('admin.index');
        });
    });
