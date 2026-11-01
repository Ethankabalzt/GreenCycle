<?php

namespace Database\Seeders;

use App\Models\SeedType;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SeedTypeSeeder::class,
        ]);

        $user = User::firstOrCreate(
            ['email' => 'demo@greencycle.test'],
            ['name' => 'Demo', 'password' => 'demo1234.']
        );

        if ($user->trees()->count() === 0) {
            Tree::create([
                'user_id' => $user->id,
                'seed_type_id' => SeedType::first()->id,
            ]);
        }
    }
}
