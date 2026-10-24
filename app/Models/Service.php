<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'tagline',
        'short_description',
        'hero_badge',
        'icon_key',
        'accent_color',
        'secondary_color',
        'hero_slides_json',
        'stats_json',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hero_slides_json' => 'array',
            'stats_json' => 'array',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ServiceRow::class)->orderBy('display_order');
    }

    public function icons(): HasMany
    {
        return $this->hasMany(ServiceIcon::class)->orderBy('display_order');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ServicePackage::class)->orderBy('display_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'interested_service_id');
    }
}
