<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasUuid;

    const UPDATED_AT = null;

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'store_id',
    ];

    public function products()
    {
        return $this->hasMany(CartProduct::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
