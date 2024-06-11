<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $connection = 'mysql_shinko';
    protected $table = 'admin_user';

    public $timestamps = false;

}
