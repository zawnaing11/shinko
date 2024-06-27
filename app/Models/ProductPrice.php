<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasUuid;

    protected $connection = 'mysql_shinko';
    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'store_id',
        'jan_cd',
        'price'
    ];
}
