<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceIcon extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'group_type',
        'title',
        'icon_name',
        'badge_text',
        'popup_title',
        'popup_summary',
        'popup_bullets_json',
        'popup_cta_text',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'popup_bullets_json' => 'array',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
