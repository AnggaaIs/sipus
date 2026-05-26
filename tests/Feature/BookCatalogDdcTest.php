<?php

use App\Models\Book;
use App\Models\Ddc;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('catalog can be filtered by ddc', function () {
    $ddcScience = Ddc::factory()->create([
        'code' => '500',
        'name' => 'Ilmu alam',
    ]);
    $ddcLiterature = Ddc::factory()->create([
        'code' => '800',
        'name' => 'Sastra',
    ]);

    $scienceBook = Book::factory()->for($ddcScience)->create([
        'title' => 'Fisika Dasar',
        'slug' => 'fisika-dasar',
    ]);
    $literatureBook = Book::factory()->for($ddcLiterature)->create([
        'title' => 'Puisi Modern',
        'slug' => 'puisi-modern',
    ]);

    $response = $this->get(route('books.index', ['ddc_id' => $ddcScience->id]));

    $response->assertSuccessful()
        ->assertSee($scienceBook->title)
        ->assertDontSee($literatureBook->title);
});
