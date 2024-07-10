<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory, HasUuid;

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * アクセストークンの有効性を独自チェックする
     *
     * @param mixed $accessToken
     * @param bool $isValid
     * @return bool
     * @link https://zenn.dev/moroshi/articles/cacba821019174
     */
    public static function isValidAccessToken($accessToken, bool $isValid): bool
    {
        // if (! $accessToken) {
        if (! $accessToken->last_used_at) {
            return $isValid;
        }

        $expiration = config('sanctum.expiration');
        return (! $expiration || $accessToken->last_used_at->gt(now()->subMinutes($expiration)));
    }
}
