<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    use HasUuid;

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'cart_id',
        'jan_cd',
        'quantity',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

}
