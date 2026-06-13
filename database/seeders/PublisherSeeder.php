<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 30 penerbit random
        Publisher::factory(30)->create();
    }
}
