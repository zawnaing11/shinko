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
        'publish_begin_datetime',
        'publish_end_datetime',
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

    public function scopePublishable(Builder $q): void
    {
        $now = Carbon::now();
        $q->where([
            ['publish_begin_datetime', '<=', $now],
            ['publish_end_datetime', '>=', $now],
        ]);
    }

}
