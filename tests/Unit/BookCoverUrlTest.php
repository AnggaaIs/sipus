<?php

use App\Models\Book;
use Tests\TestCase;

uses(TestCase::class);

it('builds cover urls from the covers disk', function () {
    config(['filesystems.disks.covers.url' => 'http://sipus.test/cover']);

    $book = new Book([
        'cover' => 'isbn/9786020324784-hujan.jpg',
    ]);

    expect($book->cover_url)->toBe('http://sipus.test/cover/isbn/9786020324784-hujan.jpg');
});

it('keeps external cover urls unchanged', function () {
    $book = new Book([
        'cover' => 'https://example.com/book.jpg',
    ]);

    expect($book->cover_url)->toBe('https://example.com/book.jpg');
});
