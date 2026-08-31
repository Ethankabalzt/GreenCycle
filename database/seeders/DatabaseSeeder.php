<?php

namespace Database\Seeders;

use App\Models\SeedType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'demo@greencycle.com'],
            [
                'name' => 'Demo',
                'password' => Hash::make('password1234.'),
                'coins' => 50,
            ]
        );

        SeedType::firstOrCreate(
            ['name' => 'Pino Común'],
            [
                'need_to_level' => 5,
                'harvest_coins' => 10,
                'description' => 'Árbol resistente básico',
            ]
        );

        SeedType::firstOrCreate(
            ['name' => 'Roble Dorado'],
            [
                'need_to_level' => 3,
                'harvest_coins' => 7,
                'description' => 'Crece rápido pero requiere más cuidado',
            ]
        );
    }
}
