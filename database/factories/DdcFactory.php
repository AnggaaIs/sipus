<?php

namespace Database\Factories;

use App\Models\Ddc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ddc>
 */
class DdcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classes = [
            0 => 'Karya Umum dan Ilmu Komputer',
            1 => 'Filsafat dan Psikologi',
            2 => 'Agama',
            3 => 'Ilmu Sosial',
            4 => 'Bahasa',
            5 => 'Sains',
            6 => 'Teknologi',
            7 => 'Seni dan Rekreasi',
            8 => 'Sastra',
            9 => 'Sejarah dan Geografi',
        ];
        $codeNumber = fake()->unique()->numberBetween(0, 999);
        $code = str_pad((string) $codeNumber, 3, '0', STR_PAD_LEFT);
        $classIndex = intdiv($codeNumber, 100);

        return [
            'code' => $code,
            'name' => $classes[$classIndex],
            'description' => "Klasifikasi DDC {$code} untuk bidang {$classes[$classIndex]}.",
        ];
    }
}