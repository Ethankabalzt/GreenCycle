<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveEffect extends Model
{
    public const VIGENT = 1;

    protected $fillable = [
        'tree_id',
        'shop_item_id',
        'effect_type',
        'activated_at',
        'expires_at',
        'active_flag',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
            'active_flag' => 'integer',
        ];
    }

    /**
     * Un efecto sigue vigente mientras el checkpoint sigue activo.
     */
    public function isVigente(): bool
    {
        return $this->active_flag === self::VIGENT;
    }

    public function tree()
    {
        return $this->belongsTo(Tree::class);
    }

    public function shopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }
}
