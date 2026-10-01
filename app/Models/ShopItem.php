<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    public const ACCELERATOR = 'ACCELERATOR';

    public const FERTILIZER = 'FERTILIZER';

    public const PROTECTOR = 'PROTECTOR';

    protected $fillable = [
        'name',
        'effect_type',
        'cost',
        'effect_duration_min',
        'description',
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function activeEffects()
    {
        return $this->hasMany(ActiveEffect::class);
    }
}
