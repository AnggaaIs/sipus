<?php

namespace Database\Factories;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publisher>
 */
class PublisherFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Pustaka Nusantara',
                'Cahaya Ilmu',
                'Bumi Aksara',
                'Pelita Pendidikan',
                'Andalas Media',
                'Cendekia Indonesia',
                'Literasi Bangsa',
                'Tunas Pustaka',
            ]),
            'city' => fake('id_ID')->city(),
        ];
    }
}
