<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory, HasUuid;

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'jan_cd',
        'quantity',
        'product_name',
        'selling_price_tax',
        'price_tax',
        'list_price',
        'list_price_tax',
        'wholesale_price',
        'wholesale_price_tax',
        'tax_rate',
    ];

    protected $attributes = [
        'tax_rate' => 0.00,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
