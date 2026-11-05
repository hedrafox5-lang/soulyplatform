<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpWishlist extends Model
{
    protected $table = 'erp_wishlists';

    protected $fillable = [
        'user_id',
        'catalog_item_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(ErpStoreCatalog::class, 'catalog_item_id');
    }
}
