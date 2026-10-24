<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'company_name',
        'business_activity',
        'interested_service_id',
        'interested_package_id',
        'source',
        'status',
        'assigned_to',
        'ai_summary',
        'notes',
    ];

    public function interestedService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'interested_service_id');
    }

    public function interestedPackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'interested_package_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }
}
