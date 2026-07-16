<?php

use App\Livewire\BookCatalog;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Black-Box Testing
|--------------------------------------------------------------------------
| Fokus: perilaku fitur publik dari sisi pengguna tanpa melihat detail
| implementasi internal.
*/

function createCatalogBook(array $attributes = []): Book
{
    $book = Book::factory()->create($attributes);
    $book->authors()->attach(Author::factory()->create());

    return $book->fresh(['authors', 'category', 'ddc', 'publisher']);
}

test('halaman beranda menampilkan konten utama dan buku unggulan', function () {
    $latestBook = createCatalogBook([
        'title' => 'Atlas Literasi Sekolah',
    ]);

    $mostBorrowedBook = createCatalogBook([
        'title' => 'Algoritma untuk Siswa',
        'available_copies' => 7,
        'total_copies' => 7,
    ]);

    $loan = Loan::factory()->create([
        'user_id' => User::factory()->member()->create()->getKey(),
    ]);

    $loan->loanItems()->create([
        'book_id' => $mostBorrowedBook->getKey(),
        'quantity' => 3,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('SIPUS')
        ->assertSeeText('Jelajahi Katalog')
        ->assertSee('id="tentang"', false)
        ->assertSeeText('Tentang SIPUS')
        ->assertSeeText('Perpustakaan sekolah, lebih mudah diakses.')
        ->assertSeeText($latestBook->title)
        ->assertSeeText($mostBorrowedBook->title);
});

test('halaman katalog tampil normal', function () {
    $scienceBook = createCatalogBook([
        'title' => 'Fisika Modern untuk Pelajar',
    ]);

    $historyBook = createCatalogBook([
        'title' => 'Sejarah Maritim Nusantara',
    ]);

    $this->get(route('books.index'))
        ->assertOk()
        ->assertSeeText('Katalog Buku')
        ->assertSeeText('Semua buku')
        ->assertSeeText($scienceBook->title)
        ->assertSeeText($historyBook->title);
});

test('pencarian livewire mendukung kata kunci buku', function () {
    $scienceBook = createCatalogBook([
        'title' => 'Fisika Modern untuk Pelajar',
    ]);

    $historyBook = createCatalogBook([
        'title' => 'Sejarah Maritim Nusantara',
    ]);

    Livewire::test(BookCatalog::class)
        ->assertSeeText($scienceBook->title)
        ->assertSeeText($historyBook->title)
        ->set('search', 'Fisika Modern')
        ->assertSeeText($scienceBook->title)
        ->assertDontSeeText($historyBook->title);
});

test('filter livewire mendukung penyaringan berdasarkan ddc', function () {
    $scienceBook = createCatalogBook([
        'title' => 'Fisika Modern untuk Pelajar',
    ]);

    $historyBook = createCatalogBook([
        'title' => 'Sejarah Maritim Nusantara',
    ]);

    Livewire::test(BookCatalog::class)
        ->assertSeeText($scienceBook->title)
        ->assertSeeText($historyBook->title)
        ->set('ddcId', $historyBook->ddc_id)
        ->assertSeeText($historyBook->title)
        ->assertDontSeeText($scienceBook->title);
});

test('halaman detail buku menampilkan metadata dan informasi ketersediaan', function () {
    $book = createCatalogBook([
        'title' => 'Basis Data untuk Pemula',
        'isbn' => '9781234567890',
        'publish_year' => 2024,
        'pages' => 240,
        'available_copies' => 4,
        'total_copies' => 6,
        'description' => 'Panduan dasar mempelajari basis data di perpustakaan sekolah.',
    ]);

    $response = $this->get(route('books.show', $book));

    $response->assertOk()
        ->assertSeeText($book->title)
        ->assertSeeText('Tersedia')
        ->assertSeeText('Penerbit')
        ->assertSeeText($book->publisher->name)
        ->assertSeeText('ISBN')
        ->assertSeeText($book->isbn)
        ->assertSeeText('Tentang buku ini');
});

test('halaman kategori menampilkan daftar kategori', function () {
    $category = Category::factory()->create([
        'name' => 'Teknologi Informasi',
        'slug' => 'teknologi-informasi',
    ]);

    $this->get(route('categories.index'))
        ->assertOk()
        ->assertSeeText('Kategori Buku')
        ->assertSeeText($category->name);
});

test('halaman detail kategori menampilkan buku di dalam kategori', function () {
    $category = Category::factory()->create([
        'name' => 'Teknologi Informasi',
        'slug' => 'teknologi-informasi',
    ]);

    $book = createCatalogBook([
        'title' => 'Jaringan Komputer Dasar',
        'category_id' => $category->getKey(),
    ]);

    $this->get(route('categories.show', $category))
        ->assertOk()
        ->assertSeeText($category->name)
        ->assertSeeText('Koleksi buku kategori ini')
        ->assertSeeText($book->title);
});

test('route fallback menampilkan halaman 404 kustom', function () {
    $this->get('/rute-yang-tidak-ada')
        ->assertNotFound()
        ->assertSeeText('Halaman tidak ditemukan')
        ->assertSeeText('Kembali ke beranda');
});
