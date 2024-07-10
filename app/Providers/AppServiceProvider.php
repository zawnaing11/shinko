<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // varcharのデフォルト文字数を191文字にする
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        Validator::extend('hiragana', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[ぁ-ゞー\s]+$/u', $value);
        }, ':attributeは、ひらがなで入力して下さい。');

        Validator::extend('katakana', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[ァ-ヴー]+/u', $value);
        }, ':attributeは、カタカナで入力して下さい。');

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Sanctum::authenticateAccessTokensUsing([PersonalAccessToken::class, 'isValidAccessToken']);
    }
}
