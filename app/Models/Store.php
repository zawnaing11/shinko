<?php

namespace App\Models;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function qrCode(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $renderer = new GDLibRenderer(600, 0, 'jpeg');
                $writer = new Writer($renderer);
                return base64_encode($writer->writeString(json_encode(['store_id' => $attributes['id']])));
            }
        );
    }

}
