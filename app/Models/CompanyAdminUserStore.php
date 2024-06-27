<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAdminUserStore extends Model
{
    protected $connection = 'mysql_shinko';
    protected $table = 'company_admin_user_stores';
    protected $primaryKey = ['company_admin_user_id', 'store_id'];

    public $incrementing = false;

    public function companyAdminUser()
    {
        return $this->belongsTo(CompanyAdminUser::class, 'company_admin_user_id');
    }

    public function storeBases()
    {
        return $this->hasMany(StoreBase::class, 'store_id', 'store_id');
    }
}
