<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Care extends Model
{
    public const WATER = 'WATER';

    public const FERTILIZE = 'FERTILIZE';

    public const PRUNE = 'PRUNE';

    public const ACCELERATE = 'ACCELERATE';

    protected $fillable = [
        'tree_id',
        'user_id',
        'action',
        'progress_gained',
        'note',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_gained' => 'integer',
            'performed_at' => 'datetime',
        ];
    }

    public function tree()
    {
        return $this->belongsTo(Tree::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
