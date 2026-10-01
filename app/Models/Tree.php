<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    //
    public const ACTIVE = 'ACTIVE';

    public const MATURE = 'MATURE';

    public const DEAD = 'DEAD';

    public const HARVESTED = 'HARVESTED';

    /**
     * Cada cuántos horas el scheduler aplica el deterioro a un árbol sin cuidado.
     */
    public const DECAY_INTERVAL_HOURS = 6;

    /**
     * Salud que pierde un árbol por cada intervalo de deterioro aplicado.
     */
    public const DECAY_DAMAGE = 20;

    protected $fillable = [
        'user_id',
        'seed_type_id',
        'level',
        'health',
        'progress',
        'status',
        'last_cared_at',
        'next_care_at',
        'last_decay_at',
        'next_decay_at',
        'harvested_at',
        'planted_at',
    ];

    protected function casts(): array
    {
        return [
            'last_cared_at' => 'datetime',
            'next_care_at' => 'datetime',
            'last_decay_at' => 'datetime',
            'next_decay_at' => 'datetime',
            'harvested_at' => 'datetime',
            'planted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seedType()
    {
        return $this->belongsTo(SeedType::class);
    }

    public function cares()
    {
        return $this->hasMany(Care::class);
    }

    public function activeEffects()
    {
        return $this->hasMany(ActiveEffect::class);
    }
}
