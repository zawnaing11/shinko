<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function storeBases()
    {
        return $this->hasMany(StoreBase::class, 'base_id', 'base_id');
    }

    public function scopeCurrent($q)
    {
        $now = Carbon::now();
        return $q->where([
            ['price_start_date', '<=', $now],
            ['price_end_date', '>=', $now],
        ]);
    }
}
