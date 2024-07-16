<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ImportDetail extends Model
{
    use HasUuid;

    const UPDATED_AT = null;

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
        'import_id',
        'line_number',
        'result',
        'messages',
        'status',
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}