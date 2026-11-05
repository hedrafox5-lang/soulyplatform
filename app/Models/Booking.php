<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'user_id',
        'service_id',
        'service_package_id',
        'assigned_staff_id',
        'quantity_or_hours',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'pipeline_stage',
        'call_confirmed',
        'call_confirmed_at',
        'required_hardware_amount',
        'chat_transcript',
        'visit_started_at',
        'actual_hours',
        'scheduled_at',
        'client_requirements',
        'staff_report',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'quantity_or_hours' => 'integer',
            'scheduled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function assignedEngineer(): BelongsTo
    {
        return $this->assignedStaff();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
