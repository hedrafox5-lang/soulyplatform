<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpSalesProposal extends Model
{
    protected $table = 'erp_sales_proposals';

    protected $fillable = [
        'user_id',
        'title',
        'notes',
        'proposed_items_json',
        'discount_percent',
        'total_before_discount',
        'total_after_discount',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'proposed_items_json'   => 'array',
            'discount_percent'      => 'decimal:2',
            'total_before_discount' => 'decimal:2',
            'total_after_discount'  => 'decimal:2',
            'expires_at'            => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
