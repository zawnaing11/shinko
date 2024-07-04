<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class CompanyAdminUser extends Authenticatable
{
    protected $connection = 'mysql_shinko';
    protected $table = 'company_admin_users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function stores()
    {
        return $this->hasMany(CompanyAdminUserStore::class);
    }
}
