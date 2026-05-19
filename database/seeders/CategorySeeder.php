<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiksi',         'description' => 'Novel, cerpen, dan karya fiksi lainnya'],
            ['name' => 'Non-Fiksi',     'description' => 'Buku faktual dan informatif'],
            ['name' => 'Sains',         'description' => 'Ilmu pengetahuan alam dan teknologi'],
            ['name' => 'Sejarah',       'description' => 'Sejarah Indonesia dan dunia'],
            ['name' => 'Matematika',    'description' => 'Buku pelajaran matematika'],
            ['name' => 'Bahasa',        'description' => 'Bahasa Indonesia dan bahasa asing'],
            ['name' => 'Agama',         'description' => 'Buku keagamaan dan spiritual'],
            ['name' => 'Biografi',      'description' => 'Kisah hidup tokoh-tokoh inspiratif'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
