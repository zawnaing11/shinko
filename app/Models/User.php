<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    use HasUuid;

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'company_id',
        'email',
        'password',
        'name',
        'is_active',
    ];

    public function setPasswordAttribute($value)
    {
        if (! empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
