<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use App\Models\Ddc;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->randomElement([
            'Jejak Langkah di Tanah Minang',
            'Mengenal Kekayaan Alam Indonesia',
            'Petualangan di Negeri Seribu Pulau',
            'Rahasia Belajar dengan Efektif',
            'Sejarah Perjuangan Bangsa Indonesia',
            'Teknologi untuk Masa Depan',
            'Cerita dari Kampung Halaman',
            'Sains dalam Kehidupan Sehari-hari',
            'Menjaga Bumi Tetap Lestari',
            'Matematika Itu Menyenangkan',
            'Menyusuri Sungai Batang Hari',
            'Kisah Inspiratif Pelajar Nusantara',
            'Budaya dan Tradisi Indonesia',
            'Dasar-Dasar Pemrograman Komputer',
            'Membangun Kebiasaan Membaca',
            'Pahlawan dari Ranah Minang',
            'Dunia Hewan dan Tumbuhan',
            'Bahasa Indonesia untuk Pelajar',
            'Ekonomi Kreatif Generasi Muda',
            'Cahaya dari Ujung Desa',
            'Ensiklopedia Mini Nusantara',
            'Misteri Rumah Tua di Bukittinggi',
            'Kumpulan Cerita Rakyat Sumatera Barat',
            'Fisika di Sekitar Kita',
            'Kimia untuk Kehidupan',
            'Biologi dan Keanekaragaman Hayati',
            'Belajar Mandiri Meraih Prestasi',
            'Perjalanan Menuju Cita-Cita',
            'Mengenal Tata Surya',
            'Seni Berkomunikasi dengan Baik',
            'Pemimpin Muda Indonesia',
            'Warisan Kuliner Nusantara',
            'Laut dan Kehidupan Pesisir',
            'Dongeng Sebelum Tidur dari Nusantara',
            'Panduan Menulis Cerita Pendek',
            'Kecerdasan Buatan untuk Pemula',
            'Mengenal Dunia Kewirausahaan',
            'Atlas Sejarah Indonesia',
            'Hidup Sehat untuk Remaja',
            'Bintang di Langit Padang',
        ]);
        $totalCopies = fake()->numberBetween(1, 10);
        $availableCopies = fake()->numberBetween(0, $totalCopies);

        return [
            'isbn' => fake()->unique()->numerify('978##########'),
            'title' => $title,
            'slug' => Str::slug($title),
            'category_id' => Category::factory(),
            'ddc_id' => Ddc::factory(),
            'publisher_id' => Publisher::factory(),
            'description' => fake()->randomElement([
                "Buku {$title} menyajikan pembahasan terstruktur dengan bahasa Indonesia yang mudah dipahami pelajar.",
                "Melalui buku {$title}, pembaca diajak belajar melalui contoh yang dekat dengan kehidupan sehari-hari.",
                "Buku {$title} cocok untuk menambah wawasan, melatih rasa ingin tahu, dan membangun kebiasaan belajar.",
            ]),
            'publish_year' => fake()->numberBetween(2000, (int) now()->format('Y')),
            'pages' => fake()->numberBetween(80, 600),
            'language' => 'id',
            'cover' => null,
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
        ];
    }
}
