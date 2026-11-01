<?php

namespace Database\Factories;

use App\Models\Tree;
use App\Models\User;
use App\Models\SeedType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tree>
 */
class TreeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        'user_id'      => User::factory(),
        'seed_type_id' => SeedType::factory(),
        'level'        => 0,
        'health'       => 50,
        'progress'     => 0,
        'status'       => Tree::ACTIVE,
        'planted_at'   => now(),
        'next_care_at' => null,
        ];
    }
}
