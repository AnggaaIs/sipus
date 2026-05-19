<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stock = fake()->numberBetween(1, 10);

        return [
            'categories_id'     => Category::inRandomOrder()->first()?->id
                ?? Category::factory(),
            'isbn'            => fake()->unique()->isbn13(),
            'title'           => fake()->sentence(3),
            'author'          => fake()->name(),
            'publisher'       => fake()->company(),
            'tahun_terbit'    => fake()->year(),
            'stock'           => $stock,
            'stock_available' => $stock, // awal sama dengan stock
            'description'     => fake()->paragraph(),
        ];
    }
}
