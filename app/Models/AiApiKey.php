<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AiApiKey extends Model
{
    use HasFactory;

    protected $table = 'ai_api_keys';

    protected $fillable = [
        'label',
        'account_email',
        'provider',
        'api_key',
        'scope',
        'priority',
        'status',
        'cooldown_until',
        'requests_today',
        'total_requests',
        'last_latency_ms',
        'last_used_at',
        'last_error',
    ];

    protected $casts = [
        'cooldown_until' => 'datetime',
        'last_used_at' => 'datetime',
        'requests_today' => 'integer',
        'total_requests' => 'integer',
        'last_latency_ms' => 'integer',
        'priority' => 'integer',
    ];

    /**
     * Check if key is currently in cooldown.
     */
    public function isCooldown(): bool
    {
        if ($this->status === 'cooldown') {
            if ($this->cooldown_until && $this->cooldown_until->isFuture()) {
                return true;
            }
            // Cooldown has expired, auto-activate
            $this->update(['status' => 'active', 'cooldown_until' => null]);
            return false;
        }

        return false;
    }

    /**
     * Put key in temporary cooldown (e.g. rate limit 429).
     */
    public function markCooldown(int $minutes = 15, ?string $error = null): void
    {
        $this->update([
            'status' => 'cooldown',
            'cooldown_until' => Carbon::now()->addMinutes($minutes),
            'last_error' => $error ? substr($error, 0, 1000) : 'Rate limit / quota exceeded',
        ]);
    }

    /**
     * Record successful API request and update latency.
     */
    public function recordUsage(?int $latencyMs = null): void
    {
        $this->increment('requests_today');
        $this->increment('total_requests');
        $this->update([
            'last_used_at' => Carbon::now(),
            'last_latency_ms' => $latencyMs,
            'status' => 'active',
            'cooldown_until' => null,
            'last_error' => null,
        ]);
    }
}
