<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeedType extends Model
{
    protected $fillable = [
        'name',
        'cares_by_level',
        'harvest_coins',
    ];

    public function trees()
    {
        return $this->hasMany(Tree::class);
    }
}
