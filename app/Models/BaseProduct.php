<?php

namespace App\Models;

use App\Traits\TaxTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BaseProduct extends Model
{
    use TaxTrait;

    protected $connection = 'mysql_shinko';
    protected $table = 'base_products';
    protected $primaryKey = ['base_id', 'jan_cd', 'price_start_date'];

    public $incrementing = false;

    public function msProduct()
    {
        return $this->belongsTo(MsProduct::class, 'jan_cd', 'jan_cd');
    }

    public function storeBases()
    {
        return $this->hasMany(StoreBase::class, 'base_id', 'base_id');
    }

    protected function listPriceTaxCalc(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['list_price_tax'] ?: $this->calcTax($attributes['list_price'], $this->msProduct->tax_rate),
        );
    }

    protected function wholesalePriceTaxCalc(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (float) $attributes['wholesale_price_tax'] ?: $this->calcTax($attributes['wholesale_price'], $this->msProduct->tax_rate),
        );
    }

    public function scopeCurrent($q)
    {
        $today = Carbon::today();
        return $q->where([
            ['price_start_date', '<=', $today],
            ['price_end_date', '>=', $today],
        ]);
    }
}
