<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Care extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tree_id',
        'type',
        'cared_at',
    ];
    protected $casts = [
        'cared_at' => 'datetime'
    ];

    public function tree(): BelongsTo
    {
        return $this ->belongsTo(Tree::class);
    }
}
