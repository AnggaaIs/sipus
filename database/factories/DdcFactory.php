<?php

namespace Database\Factories;

use App\Models\Ddc;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ddc>
 */
class DdcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = str_pad((string) fake()->unique()->numberBetween(0, 999), 3, '0', STR_PAD_LEFT);

        return [
            'code' => $code,
            'name' => Str::title(fake()->words(rand(2, 4), true)),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
