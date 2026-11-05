<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpStoreCatalog extends Model
{
    protected $table = 'erp_store_catalog';

    protected $fillable = [
        'tier',
        'code',
        'name',
        'name_ar',
        'slug',
        'description',
        'short_desc_ar',
        'full_details_ar',
        'department',
        'department_ar',
        'compatible_industries',
        'price',
        'original_price',
        'roi_time_saved_ar',
        'activations_count',
        'is_flash_deal',
        'is_sales_engineer_pick',
        'related_hardware_slug',
        'badge',
        'icon_name',
        'icon_svg',
        'features_json',
        'included_items',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'price'                 => 'decimal:2',
            'original_price'        => 'decimal:2',
            'compatible_industries' => 'array',
            'features_json'         => 'array',
            'included_items'        => 'array',
            'activations_count'     => 'integer',
            'is_flash_deal'         => 'boolean',
            'is_sales_engineer_pick'=> 'boolean',
            'is_active'             => 'boolean',
            'display_order'         => 'integer',
        ];
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(ErpWishlist::class, 'catalog_item_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeFlashDeals($query)
    {
        return $query->where('is_flash_deal', true);
    }

    public function scopeSalesEngineerPicks($query)
    {
        return $query->where('is_sales_engineer_pick', true);
    }

    public function scopeForIndustry($query, string $industry)
    {
        return $query->whereJsonContains('compatible_industries', $industry);
    }
}
