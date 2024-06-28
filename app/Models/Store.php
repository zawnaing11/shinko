<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $connection = 'mysql_shinko';
    protected $table = 'store';
    protected $primaryKey = 'id';

    public $incrementing = false;

    public function storeBases()
    {
        return $this->hasMany(StoreBase::class);
    }

    public function companyAdminUserStore()
    {
        return $this->hasOne(CompanyAdminUserStore::class);
    }

}
