<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasUuid;

    // PRIMARY KEY uuid 設定
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'messages' => 'array'
    ];

    protected $attributes = [
        'messages' => '[]',
    ];

    protected $fillable = [
        'job_id',
        'model_name',
        'file_name',
        'status',
        'messages',
        'deleted_at',
    ];

    public function details()
    {
        return $this->hasMany(ImportDetail::class);
    }
}
