<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use Illuminate\Database\Seeder;

class ShopItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShopItem::create([
            'name' => 'Acelerador de crecimiento',
            'effect_type' => ShopItem::ACCELERATOR,
            'cost' => 10,
            'effect_duration_min' => 60,
            'description' => 'Acelera el crecimiento del árbol durante una hora.',
        ]);

        ShopItem::create([
            'name' => 'Fertilizante',
            'effect_type' => ShopItem::FERTILIZER,
            'cost' => 15,
            'effect_duration_min' => 120,
            'description' => 'Aumenta el progreso de cuidado durante dos horas.',
        ]);

        ShopItem::create([
            'name' => 'Protector',
            'effect_type' => ShopItem::PROTECTOR,
            'cost' => 20,
            'effect_duration_min' => 240,
            'description' => 'Reduce el deterioro del árbol durante cuatro horas.',
        ]);
    }
}
