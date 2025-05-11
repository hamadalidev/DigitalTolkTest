<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale_id',
        'key',
        'value',
        'device_type',
        'group',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const DEVICE_TYPES = [
        'mobile',
        'tablet',
        'desktop'
    ];

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
} 