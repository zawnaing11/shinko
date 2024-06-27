<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseProduct extends Model
{
    protected $connection = 'mysql_shinko';
    protected $table = 'base_products';
    protected $primaryKey = ['base_id', 'jan_cd', 'price_start_date'];

    public $incrementing = false;

    public function msProduct()
    {
        return $this->belongsTo(MsProduct::class, 'jan_cd', 'jan_cd');
    }

    public function StoreBases()
    {
        return $this->hasMany(StoreBase::class, 'base_id', 'base_id');
    }
}
