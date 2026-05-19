<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            // Fiksi
            [
                'category_id'     => Category::where('name', 'Fiksi')->first()->id,
                'isbn'            => '978-602-03-1234-5',
                'title'           => 'Laskar Pelangi',
                'author'          => 'Andrea Hirata',
                'publisher'       => 'Bentang Pustaka',
                'publication_year'    => 2005,
                'stock'           => 5,
                'stock_available' => 5,
                'description'     => 'Novel tentang semangat anak-anak Belitung dalam meraih mimpi.',
            ],
            [
                'category_id'     => Category::where('name', 'Fiksi')->first()->id,
                'isbn'            => '978-602-03-1235-2',
                'title'           => 'Bumi Manusia',
                'author'          => 'Pramoedya Ananta Toer',
                'publisher'       => 'Hasta Mitra',
                'publication_year'    => 1980,
                'stock'           => 3,
                'stock_available' => 3,
                'description'     => 'Novel sejarah tentang perjuangan di masa kolonial Belanda.',
            ],
            [
                'category_id'     => Category::where('name', 'Fiksi')->first()->id,
                'isbn'            => '978-602-03-1236-9',
                'title'           => 'Negeri 5 Menara',
                'author'          => 'Ahmad Fuadi',
                'publisher'       => 'Gramedia',
                'publication_year'    => 2009,
                'stock'           => 4,
                'stock_available' => 4,
            ],

            // Sains
            [
                'category_id'     => Category::where('name', 'Sains')->first()->id,
                'isbn'            => '978-602-03-2234-4',
                'title'           => 'Fisika Dasar',
                'author'          => 'Halliday & Resnick',
                'publisher'       => 'Erlangga',
                'publication_year'    => 2010,
                'stock'           => 6,
                'stock_available' => 6,
            ],
            [
                'category_id'     => Category::where('name', 'Sains')->first()->id,
                'isbn'            => '978-602-03-2235-1',
                'title'           => 'Kimia Organik',
                'author'          => 'Fessenden',
                'publisher'       => 'Erlangga',
                'publication_year'    => 2011,
                'stock'           => 4,
                'stock_available' => 4,
            ],

            // Sejarah
            [
                'category_id'     => Category::where('name', 'Sejarah')->first()->id,
                'isbn'            => '978-602-03-3234-3',
                'title'           => 'Sejarah Indonesia Modern',
                'author'          => 'M.C. Ricklefs',
                'publisher'       => 'Gadjah Mada University Press',
                'publication_year'    => 2008,
                'stock'           => 3,
                'stock_available' => 3,
            ],

            // Matematika
            [
                'category_id'     => Category::where('name', 'Matematika')->first()->id,
                'isbn'            => '978-602-03-4234-2',
                'title'           => 'Matematika SMA Kelas X',
                'author'          => 'Marthen Kanginan',
                'publisher'       => 'Erlangga',
                'publication_year'    => 2016,
                'stock'           => 8,
                'stock_available' => 8,
            ],
            [
                'category_id'     => Category::where('name', 'Matematika')->first()->id,
                'isbn'            => '978-602-03-4235-9',
                'title'           => 'Matematika SMA Kelas XI',
                'author'          => 'Marthen Kanginan',
                'publisher'       => 'Erlangga',
                'publication_year'    => 2017,
                'stock'           => 7,
                'stock_available' => 7,
            ],

            // Biografi
            [
                'category_id'     => Category::where('name', 'Biografi')->first()->id,
                'isbn'            => '978-602-03-5234-1',
                'title'           => 'Soekarno: Biografi Sang Proklamator',
                'author'          => 'Cindy Adams',
                'publisher'       => 'Media Pressindo',
                'publication_year'    => 2014,
                'stock'           => 2,
                'stock_available' => 2,
            ],

            // Bahasa
            [
                'category_id'     => Category::where('name', 'Bahasa')->first()->id,
                'isbn'            => '978-602-03-6234-0',
                'title'           => 'Kamus Besar Bahasa Indonesia',
                'author'          => 'Tim Penyusun KBBI',
                'publisher'       => 'Balai Pustaka',
                'publication_year'    => 2020,
                'stock'           => 3,
                'stock_available' => 3,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
