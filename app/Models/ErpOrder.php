<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ErpOrder extends Model
{
    protected $table = 'erp_orders';

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'items_json',
        'total_amount',
        'payment_method',
        'status',
        'license_key',
        'is_trial',
        'trial_expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'items_json'        => 'array',
            'total_amount'      => 'decimal:2',
            'is_trial'          => 'boolean',
            'trial_expires_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ErpOrderItem::class, 'order_id');
    }
}
