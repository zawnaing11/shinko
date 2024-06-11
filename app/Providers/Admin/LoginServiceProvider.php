<?php

namespace App\Providers\Admin;

use App\Http\Controllers\Admin\Auth\LoginController;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use App\Actions\Admin\AttemptToAuthenticate;
use Illuminate\Support\ServiceProvider;

class LoginServiceProvider extends ServiceProvider
{
    /**
    * Register services.
    *
    * @return void
    */
    public function register()
    {
        $this->app
            ->when([LoginController::class, AttemptToAuthenticate::class])
            ->needs(StatefulGuard::class)
            ->give(function () {
                return Auth::guard('admin');
            });
    }

    /**
    * Bootstrap services.
    *
    * @return void
    */
    public function boot()
    {
        //
    }
}
