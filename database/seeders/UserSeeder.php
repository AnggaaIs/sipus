<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name'      => 'Admin SIPUS',
            'full_name' => 'Administrator SIPUS',
            'email'     => 'admin@sipus.com',
            'password'  => Hash::make('password'),
        ]);

        User::factory()->member()->create([
            'name' => 'Budi Santoso',
            'full_name' => 'Budi Santoso',
            'email' => 'budi.santoso@sipus.com',
            'password' => Hash::make('password'),
        ]);

        // Buat 20 siswa yang sudah approved
        User::factory(20)->member()->create();

        // Buat 5 siswa yang masih pending approval (testing UC-17)
        User::factory(5)->pendingApproval()->create();
    }
}
