<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeedType extends Model
{


    use HasFactory;
    //
    protected $fillable = [
        'name',
        'need_to_level',
        'harvest_coins',
        'description'
    ];

    public function trees()
    {
        return $this->hasMany(Tree::class);
    }
}
