<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErpCustomRequest extends Model
{
    protected $table = 'erp_custom_requests';

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'industry',
        'feature_title',
        'feature_description',
        'budget_range',
        'status',
        'admin_response',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
