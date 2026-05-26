<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            DdcSeeder::class,
            AuthorSeeder::class,
            PublisherSeeder::class,
            BookSeeder::class,
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin Sipus',
            'full_name' => 'Admin Sipus',
            'email' => 'admin@sipus.test',
        ]);

        User::factory()->member()->create([
            'name' => 'User Sipus',
            'full_name' => 'User Sipus',
            'email' => 'user@sipus.test',
        ]);
    }
}
