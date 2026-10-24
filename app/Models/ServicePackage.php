<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'slug',
        'name',
        'subtitle',
        'badge_text',
        'billing_type',
        'original_price',
        'price',
        'min_units',
        'unit_label',
        'warranty_days',
        'features_json',
        'subpage_sections_json',
        'is_featured',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'original_price' => 'decimal:2',
            'price' => 'decimal:2',
            'min_units' => 'integer',
            'warranty_days' => 'integer',
            'features_json' => 'array',
            'subpage_sections_json' => 'array',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'interested_package_id');
    }
}
