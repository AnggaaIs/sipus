<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Ddc;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $authors = Author::query()->get();

        if ($authors->isEmpty()) {
            $authors = Author::factory(30)->create();
        }

        $categories = Category::query()->get();
        $ddcs = Ddc::query()->get();
        $publishers = Publisher::query()->get();

        Book::factory(30)
            ->state(fn (): array => [
                'category_id' => $categories->random()->getKey(),
                'ddc_id' => $ddcs->random()->getKey(),
                'publisher_id' => $publishers->random()->getKey(),
            ])
            ->create()
            ->each(function (Book $book): void {
                $book->update([
                    'available_copies' => $book->total_copies,
                ]);
            })
            ->each(function (Book $book) use ($authors): void {
                $book->authors()->attach(
                    $authors
                        ->shuffle()
                        ->take(fake()->numberBetween(1, min(3, $authors->count())))
                        ->modelKeys(),
                );
            });
    }
}
