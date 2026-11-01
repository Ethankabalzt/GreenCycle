<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeedType extends Model
{
    /** @use HasFactory<\Database\Factories\SeedTypeFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'cares_by_level', 'harvest_coins', 'description'];

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }
}
