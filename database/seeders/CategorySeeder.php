<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiksi', 'description' => 'Novel, cerpen, dan cerita rekaan.', 'color' => '#8b5cf6'],
            ['name' => 'Sains', 'description' => 'Buku ilmu pengetahuan alam dan penelitian.', 'color' => '#0ea5e9'],
            ['name' => 'Teknologi', 'description' => 'Komputer, rekayasa, dan perkembangan teknologi.', 'color' => '#06b6d4'],
            ['name' => 'Sejarah', 'description' => 'Peristiwa dan tokoh sejarah Indonesia maupun dunia.', 'color' => '#f59e0b'],
            ['name' => 'Pendidikan', 'description' => 'Materi belajar dan pengembangan kemampuan pelajar.', 'color' => '#22c55e'],
            ['name' => 'Budaya', 'description' => 'Tradisi, seni, dan kebudayaan Nusantara.', 'color' => '#ec4899'],
            ['name' => 'Agama', 'description' => 'Pengetahuan agama dan pembentukan karakter.', 'color' => '#14b8a6'],
            ['name' => 'Referensi', 'description' => 'Ensiklopedia, kamus, atlas, dan buku rujukan.', 'color' => '#64748b'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => str($category['name'])->slug()],
                $category,
            );
        }
    }
}
