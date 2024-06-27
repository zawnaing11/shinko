<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseProduct extends Model
{
    protected $connection = 'mysql_shinko';
    protected $table = 'base_products';

}
