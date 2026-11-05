<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HardwareOrder extends Model
{
    use HasFactory;

    protected $table = 'hardware_orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'items_json',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'serial_numbers_notes',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'items_json'   => 'array',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(HardwareOrderItem::class, 'order_id');
    }

    public function getStatusLabelArAttribute(): string
    {
        return match ($this->status) {
            'pending_review'            => 'قيد المراجعة والاعتماد',
            'confirmed_preparing'       => 'جاري التجهيز والفحص الهندسي',
            'out_for_delivery'          => 'خرج للتوصيل / مع المهندس',
            'delivered_warranty_active' => 'تم التسليم وتفعيل الضمان',
            'cancelled'                 => 'ملغي',
            default                     => 'قيد المعالجة',
        };
    }

    public function getStepIndexAttribute(): int
    {
        return match ($this->status) {
            'pending_review'            => 1,
            'confirmed_preparing'       => 2,
            'out_for_delivery'          => 3,
            'delivered_warranty_active' => 4,
            default                     => 1,
        };
    }
}
