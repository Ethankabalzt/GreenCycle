<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([SeedTypeSeeder::class]);

        // Usuario de prueba documentado en el README (solo en local)
        if (app()->environment('local')) {
            User::factory()->create([
                'name' => 'demo',
                'email' => 'demo@greencycle.test',
                'password' => 'demo1234.',
            ]);
        }
    }
}
