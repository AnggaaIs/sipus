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
        $code = fake()->randomElement(array_keys($classes));

        return [
            'code' => $code,
            'name' => $classes[$code],
            'description' => "Klasifikasi DDC {$code} untuk bidang {$classes[$code]}.",
        ];
    }
}
