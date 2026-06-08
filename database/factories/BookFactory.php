<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use App\Models\Ddc;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(rand(2, 4), true));
        $totalCopies = fake()->numberBetween(1, 10);
        $availableCopies = fake()->numberBetween(0, $totalCopies);

        return [
            'isbn' => fake()->unique()->numerify('978##########'),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numerify('##'),
            'category_id' => Category::factory(),
            'ddc_id' => Ddc::factory(),
            'publisher_id' => Publisher::factory(),
            'description' => fake()->optional()->paragraph(),
            'publish_year' => fake()->numberBetween(2000, (int) now()->format('Y')),
            'pages' => fake()->numberBetween(80, 600),
            'language' => 'id',
            'cover' => null,
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
        ];
    }
}
