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
        $types = [
            [
                'name' => 'Roble',
                'cares_by_level' => 5,
                'harvest_coins' => 100,
                'description' => 'Árbol resistente de crecimiento estándar.',
            ],
            [
                'name' => 'Pino',
                'cares_by_level' => 4,
                'harvest_coins' => 80,
                'description' => 'Crece rápido, pero da menos monedas.',
            ],
            [
                'name' => 'Cerezo',
                'cares_by_level' => 7,
                'harvest_coins' => 180,
                'description' => 'Crece lento y da una mejor cosecha.',
            ],
        ];

        foreach ($types as $type) {
            SeedType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
