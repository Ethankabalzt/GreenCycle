<?php

namespace Database\Seeders;

use App\Models\SeedType;
use Illuminate\Database\Seeder;

class SeedTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeedType::create([
            'name' => 'Árbol básico',
            'cares_by_level' => 5,
            'harvest_coins' => 10,
        ]);

        SeedType::create([
            'name' => 'Semilla especial',
            'cares_by_level' => 3,
            'harvest_coins' => 7,
        ]);
    }
}
