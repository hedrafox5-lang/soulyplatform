<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpOrderItem extends Model
{
    protected $table = 'erp_order_items';

    protected $fillable = [
        'order_id',
        'catalog_item_id',
        'item_code',
        'item_name',
        'tier',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ErpOrder::class, 'order_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(ErpStoreCatalog::class, 'catalog_item_id');
    }
}
