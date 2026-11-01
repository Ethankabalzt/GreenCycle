<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    /** @use HasFactory<\Database\Factories\TreeFactory> */
    use HasFactory;

    public const ACTIVE = 'ACTIVE';
    public const DEAD = 'DEAD';
    public const MATURE = 'MATURE';

    public const MAX_LEVEL = 10;
    public const HEALTH_PER_CARE = 20;
    public const COOLDOWN_MINUTES = 60;

    protected $fillable = [
        'user_id', 'seed_type_id', 'level', 'health', 'progress', 'status',
        'planted_at', 'last_cared_at', 'last_decay_at', 'next_care_at', 'harvested_at',
    ];

    protected $casts = [
        'planted_at'    => 'datetime',
        'last_cared_at' => 'datetime',
        'last_decay_at' => 'datetime',
        'next_care_at'  => 'datetime',
        'harvested_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seedType(): BelongsTo
    {
        return $this->belongsTo(SeedType::class);
    }

    public function cares(): HasMany
    {
        return $this->hasMany(Care::class);
    }
}
