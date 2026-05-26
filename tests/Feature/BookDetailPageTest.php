<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Ddc;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;

test('book detail explains borrowing is done directly at the library', function () {
    $book = new Book([
        'title' => 'Hujan',
        'slug' => 'hujan',
        'isbn' => '9786020324784',
        'language' => 'id',
        'available_copies' => 2,
        'total_copies' => 3,
    ]);

    $book->setRelation('authors', new Collection);
    $book->setRelation('category', new Category([
        'name' => 'Novel',
        'color' => '#2563eb',
    ]));
    $book->setRelation('ddc', new Ddc([
        'code' => '800',
        'name' => 'Sastra',
    ]));
    $book->setRelation('publisher', new Publisher([
        'name' => 'Gramedia Pustaka Utama',
    ]));

    $response = $this->view('books.show', [
        'book' => $book,
    ]);

    $response->assertSee('Peminjaman langsung di perpustakaan')
        ->assertSee('silakan datang langsung ke perpustakaan SMA Semen Padang')
        ->assertSee('800 - Sastra')
        ->assertDontSee('Pinjam buku ini')
        ->assertDontSee('Masuk untuk meminjam');
});
