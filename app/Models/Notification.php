<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Notification extends Model
{
    use HasUuid;

    protected $connection = 'mysql_shinko';

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'body',
        'image',
        'is_active',
        'publish_date',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(config('const.notification_image_path')) . $this->image;
        }
    }

}
