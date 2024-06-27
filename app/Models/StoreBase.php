<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreBase extends Model
{
    protected $connection = 'mysql_shinko';
    protected $table = 'store_bases';
    protected $primaryKey = ['store_id', 'base_id'];

    public $incrementing = false;

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
