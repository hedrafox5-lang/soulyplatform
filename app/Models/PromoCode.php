<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_cap',
        'max_uses_total',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'             => 'decimal:2',
            'min_order_amount'  => 'decimal:2',
            'max_discount_cap'  => 'decimal:2',
            'max_uses_total'    => 'integer',
            'max_uses_per_user' => 'integer',
            'is_active'         => 'boolean',
            'starts_at'         => 'datetime',
            'expires_at'        => 'datetime',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }
}