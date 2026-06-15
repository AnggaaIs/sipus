<?php

namespace Database\Seeders;

use App\Models\Ddc;
use Illuminate\Database\Seeder;

class DdcSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            '000' => 'Karya Umum dan Ilmu Komputer',
            '100' => 'Filsafat dan Psikologi',
            '200' => 'Agama',
            '300' => 'Ilmu Sosial',
            '400' => 'Bahasa',
            '500' => 'Sains',
            '600' => 'Teknologi',
            '700' => 'Seni dan Rekreasi',
            '800' => 'Sastra',
            '900' => 'Sejarah dan Geografi',
        ];

        foreach ($classes as $code => $name) {
            Ddc::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => "Klasifikasi DDC {$code} untuk bidang {$name}.",
                ],
            );
        }
    }
}
