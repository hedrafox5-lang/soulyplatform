<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_name',
        'item_code',
        'quantity',
        'unit_price',
        'discount',
        'total_price',
        'warranty_days',
        'warranty_policy',
    ];

    protected function casts(): array
    {
        return [
            'unit_price'    => 'decimal:2',
            'discount'      => 'decimal:2',
            'total_price'   => 'decimal:2',
            'quantity'      => 'integer',
            'warranty_days' => 'integer',
        ];
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->total_price;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function warranty(): HasOne
    {
        return $this->hasOne(Warranty::class);
    }
}