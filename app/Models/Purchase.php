<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'shop_item_id',
        'quantity',
        'coins_spent',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'coins_spent' => 'integer',
            'purchased_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shopItem()
    {
        return $this->belongsTo(ShopItem::class);
    }
}
