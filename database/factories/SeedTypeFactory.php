<?php

namespace Database\Factories;

use App\Models\SeedType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeedType>
 */
class SeedTypeFactory extends Factory
{
    protected $model = SeedType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'need_to_level' => fake()->numberBetween(1, 10),
            'harvest_coins' => fake()->numberBetween(1, 20),
            'description' => fake()->sentence(),
        ];
    }
}
