<?php

namespace Database\Seeders;

use App\Models\Ddc;
use Illuminate\Database\Seeder;

class DdcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['code' => '000', 'name' => 'Karya umum', 'description' => 'Komputer, informasi, dan karya umum.'],
            ['code' => '100', 'name' => 'Filsafat & psikologi', 'description' => 'Filsafat, parapsikologi, dan psikologi.'],
            ['code' => '200', 'name' => 'Agama', 'description' => 'Agama, teologi, dan kepercayaan.'],
            ['code' => '300', 'name' => 'Ilmu sosial', 'description' => 'Ilmu sosial, statistik, politik, ekonomi, hukum, dan pendidikan.'],
            ['code' => '400', 'name' => 'Bahasa', 'description' => 'Bahasa dan linguistik.'],
            ['code' => '500', 'name' => 'Ilmu alam', 'description' => 'Matematika, ilmu alam, dan sains.'],
            ['code' => '600', 'name' => 'Teknologi', 'description' => 'Teknologi, kesehatan, teknik, pertanian, manajemen.'],
            ['code' => '700', 'name' => 'Seni & rekreasi', 'description' => 'Seni, olahraga, dan hiburan.'],
            ['code' => '800', 'name' => 'Sastra', 'description' => 'Sastra dan kritik sastra.'],
            ['code' => '900', 'name' => 'Sejarah & geografi', 'description' => 'Sejarah, geografi, dan biografi.'],
        ])->each(function (array $ddc): void {
            Ddc::query()->firstOrCreate(
                ['code' => $ddc['code']],
                [
                    'name' => $ddc['name'],
                    'description' => $ddc['description'],
                ],
            );
        });
    }
}
