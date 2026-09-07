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

    protected $fillable = [
        'user_id',
        'seed_type_id',
        'level',
        'health',
        'progress',
        'status',
        'last_cared_at',
        'next_care_at',
        'harvested_at',
        'planted_at',
    ];

    protected function casts(): array
    {
        return [
            'last_cared_at' => 'datetime',
            'next_care_at' => 'datetime',
            'harvested_at' => 'datetime',
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
}
