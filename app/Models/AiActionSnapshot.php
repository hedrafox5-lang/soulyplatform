<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiActionSnapshot extends Model
{
    use HasFactory;

    protected $table = 'ai_action_snapshots';

    protected $fillable = [
        'admin_id',
        'action_type',
        'target_table',
        'target_id',
        'before_state_json',
        'after_state_json',
        'description_ar',
        'is_reverted',
    ];

    protected $casts = [
        'before_state_json' => 'array',
        'after_state_json' => 'array',
        'is_reverted' => 'boolean',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
