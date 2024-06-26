<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CompanyAdminUser extends Authenticatable
{
    protected $connection = 'mysql_shinko';

    public $timestamps = false;

}
