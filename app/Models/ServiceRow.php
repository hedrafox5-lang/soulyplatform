<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'row_key',
        'row_type',
        'title',
        'subtitle',
        'payload_json',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
