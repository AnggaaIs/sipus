<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake('id_ID')->name();

        return [
            'name' => $name,
            'bio' => fake()->optional()->randomElement([
                "{$name} dikenal melalui karya yang membahas pendidikan dan kehidupan masyarakat Indonesia.",
                "{$name} merupakan penulis Indonesia yang aktif menulis buku pengetahuan dan bacaan umum.",
                "{$name} menulis untuk membantu pembaca memahami ilmu pengetahuan dengan bahasa yang mudah.",
            ]),
        ];
    }
}
