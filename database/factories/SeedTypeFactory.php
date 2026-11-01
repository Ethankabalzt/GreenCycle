<?php

namespace Database\Factories;

use App\Models\SeedType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeedType>
 */
class SeedTypeFactory extends Factory
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
        'name'           => fake()->unique()->word(),
        'cares_by_level' => 5,
        'harvest_coins'  => 100,
        'description'    => fake()->sentence(),
        ];
    }
}
