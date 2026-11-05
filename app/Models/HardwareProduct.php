<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HardwareProduct extends Model
{
    use HasFactory;

    protected $table = 'hardware_products';

    protected $fillable = [
        'slug',
        'sku',
        'name_ar',
        'category',
        'brand',
        'price',
        'old_price',
        'stock_qty',
        'warranty_months',
        'specs_json',
        'image_svg_or_url',
        'description_ar',
        'compatible_with_branderp',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'                   => 'decimal:2',
            'old_price'               => 'decimal:2',
            'stock_qty'               => 'integer',
            'warranty_months'         => 'integer',
            'specs_json'              => 'array',
            'compatible_with_branderp'=> 'boolean',
            'is_active'               => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(HardwareOrderItem::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getCategoryNameArAttribute(): string
    {
        return match ($this->category) {
            'pos_equipment'       => 'أجهزة كاشير ونقاط بيع',
            'storage_ram'         => 'هاردات SSD ورامات فائقة السرعة',
            'networking_security' => 'شبكات وأجهزة بصمة وتدريع أمني',
            'workstations'        => 'أجهزة ولابتوبات أعمال هندسية',
            default               => 'أجهزة ومعدات تكنولوجية',
        };
    }
}
