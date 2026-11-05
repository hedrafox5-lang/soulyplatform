<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDevice extends Model
{
    protected $fillable = [
        'user_id',
        'model_number',
        'details',
        'images_json',
        'status',
        'last_maintenance_type',
    ];

    protected $casts = [
        'images_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}