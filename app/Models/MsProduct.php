<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsProduct extends Model
{
    protected $connection = 'mysql_shinko';
    protected $table = 'ms_products';
    protected $primaryKey = 'jan_cd';

    public $incrementing = false;

    public function baseProduct()
    {
        return $this->hasMany(BaseProduct::class);
    }
}
