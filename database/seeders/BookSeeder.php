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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (
            Category::query()->doesntExist() ||
            Ddc::query()->doesntExist() ||
            Author::query()->doesntExist() ||
            Publisher::query()->doesntExist()
        ) {
            return;
        }

        $authorIds = Author::query()->pluck('id');
        $ddcIds = Ddc::query()->pluck('id');

        Book::factory()
            ->count(20)
            ->state(fn (): array => [
                'ddc_id' => $ddcIds->random(),
            ])
            ->create()
            ->each(function (Book $book) use ($authorIds): void {
                $book->authors()->attach(
                    $authorIds
                        ->shuffle()
                        ->take(min(3, $authorIds->count()))
                        ->all(),
                );
            });
    }
}
