<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Notification extends Model
{
    use HasUuid;

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

    protected $casts = [
        'publish_date' => 'datetime',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url(config('const.notifications.image_path')) . $this->image;
        }
    }

    public function scopeActive(Builder $q): void
    {
        $q->where('is_active', 1)
            ->where('publish_date', '<=', Carbon::now());
    }

}
