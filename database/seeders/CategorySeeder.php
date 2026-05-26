<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            'Novel',
            'Sains',
            'Sejarah',
            'Teknologi',
            'Bahasa',
        ])->each(function (string $name): void {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => 'Kategori '.$name,
                    'color' => fake()->hexColor(),
                ],
            );
        });
    }
}
