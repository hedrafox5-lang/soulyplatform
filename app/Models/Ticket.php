<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'booking_id',
        'assigned_to',
        'subject',
        'department',
        'department_scope',
        'reference_code',
        'priority',
        'status',
    ];

    public function getScopeLabelArAttribute(): string
    {
        return match ($this->department_scope) {
            'branderp_store'    => 'منظومة ومتجر BrandERP',
            'hardware_store'    => 'متجر قطع الهاردوير والأجهزة',
            'services_bookings' => 'متجر الحجوزات والخدمات الميدانية',
            'academy_courses'   => 'أكاديمية سولي والكورسات',
            'wallet_billing'    => 'المحفظة الرقمية والمدفوعات',
            default             => 'الدعم الفني العام',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function latestReply(): HasOne
    {
        return $this->hasOne(TicketMessage::class)->latestOfMany();
    }
}
