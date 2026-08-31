<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seed_type_id',
        'level',
        'health',
        'progress',
        'status',
        'planted_at',
        'last_cared_at',
        'next_care_at',
        'harvested_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seedType()
    {
        return $this->belongsTo(SeedType::class);
    }
}
