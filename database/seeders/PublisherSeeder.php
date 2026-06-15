<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = [
            ['name' => 'Pustaka Nusantara', 'city' => 'Jakarta'],
            ['name' => 'Cahaya Ilmu', 'city' => 'Bandung'],
            ['name' => 'Bumi Aksara', 'city' => 'Jakarta'],
            ['name' => 'Pelita Pendidikan', 'city' => 'Yogyakarta'],
            ['name' => 'Andalas Media', 'city' => 'Padang'],
            ['name' => 'Cendekia Indonesia', 'city' => 'Surabaya'],
            ['name' => 'Literasi Bangsa', 'city' => 'Malang'],
            ['name' => 'Tunas Pustaka', 'city' => 'Semarang'],
            ['name' => 'Ranah Minang Press', 'city' => 'Bukittinggi'],
            ['name' => 'Samudra Ilmu', 'city' => 'Makassar'],
        ];

        foreach ($publishers as $publisher) {
            Publisher::query()->updateOrCreate(
                ['name' => $publisher['name']],
                $publisher,
            );
        }
    }
}
