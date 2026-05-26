<?php

use App\Services\Books\BookMetadataLookup;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

it('fills book metadata from Google Books by ISBN', function () {
    Storage::fake('covers');

    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response([
            'items' => [
                [
                    'volumeInfo' => [
                        'title' => 'Clean Code',
                        'authors' => ['Robert C. Martin'],
                        'publisher' => 'Prentice Hall',
                        'publishedDate' => '2008-08-01',
                        'description' => '<p>A handbook of agile software craftsmanship.</p>',
                        'pageCount' => 464,
                        'language' => 'en',
                        'imageLinks' => [
                            'thumbnail' => 'https://covers.example/google.jpg',
                        ],
                    ],
                ],
            ],
        ]),
        'https://openlibrary.org/api/books*' => Http::response([]),
        'https://covers.example/google.jpg' => Http::response('cover-bytes', 200, [
            'Content-Type' => 'image/jpeg',
        ]),
    ]);

    $metadata = app(BookMetadataLookup::class)->lookup('9780132350884');

    expect($metadata)
        ->not->toBeNull()
        ->title->toBe('Clean Code')
        ->description->toBe('A handbook of agile software craftsmanship.')
        ->publish_year->toBe(2008)
        ->pages->toBe(464)
        ->language->toBe('en')
        ->publisher->toBe('Prentice Hall')
        ->authors->toBe(['Robert C. Martin'])
        ->source->toBe('Google Books');

    Storage::disk('covers')->assertExists($metadata['cover_path']);
});

it('falls back to Open Library when Google Books has no result', function () {
    Storage::fake('covers');

    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response([
            'items' => [],
        ]),
        'https://openlibrary.org/api/books*' => Http::response([
            'ISBN:9786020324784' => [
                'title' => 'Laskar Pelangi',
                'authors' => [
                    ['name' => 'Andrea Hirata'],
                ],
                'publishers' => [
                    ['name' => 'Bentang Pustaka'],
                ],
                'publish_date' => '2005',
                'number_of_pages' => 534,
                'languages' => [
                    ['key' => '/languages/ind'],
                ],
                'cover' => [
                    'medium' => 'https://covers.example/open-library.png',
                ],
            ],
        ]),
        'https://covers.example/open-library.png' => Http::response('png-cover-bytes', 200, [
            'Content-Type' => 'image/png',
        ]),
    ]);

    $metadata = app(BookMetadataLookup::class)->lookup('978-602-03-2478-4');

    expect($metadata)
        ->not->toBeNull()
        ->title->toBe('Laskar Pelangi')
        ->publish_year->toBe(2005)
        ->pages->toBe(534)
        ->language->toBe('id')
        ->publisher->toBe('Bentang Pustaka')
        ->authors->toBe(['Andrea Hirata'])
        ->source->toBe('Open Library');

    Storage::disk('covers')->assertExists($metadata['cover_path']);
});

it('merges missing metadata from Open Library when Google Books result is incomplete', function () {
    Storage::fake('covers');

    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response([
            'items' => [
                [
                    'volumeInfo' => [
                        'title' => 'Pemrograman Laravel',
                        'authors' => ['Tim Google'],
                        'language' => 'id',
                    ],
                ],
            ],
        ]),
        'https://openlibrary.org/api/books*' => Http::response([
            'ISBN:9786020324784' => [
                'title' => 'Pemrograman Laravel',
                'authors' => [
                    ['name' => 'Tim Open Library'],
                ],
                'publishers' => [
                    ['name' => 'Informatika'],
                ],
                'publish_date' => '2024',
                'number_of_pages' => 320,
                'description' => 'Panduan membangun aplikasi Laravel.',
                'languages' => [
                    ['key' => '/languages/ind'],
                ],
                'cover' => [
                    'large' => 'https://covers.example/open-library-merged.png',
                ],
            ],
        ]),
        'https://covers.example/open-library-merged.png' => Http::response('merged-cover-bytes', 200, [
            'Content-Type' => 'image/png',
        ]),
    ]);

    $metadata = app(BookMetadataLookup::class)->lookup('9786020324784');

    expect($metadata)
        ->not->toBeNull()
        ->title->toBe('Pemrograman Laravel')
        ->description->toBe('Panduan membangun aplikasi Laravel.')
        ->publish_year->toBe(2024)
        ->pages->toBe(320)
        ->language->toBe('id')
        ->publisher->toBe('Informatika')
        ->authors->toBe(['Tim Google', 'Tim Open Library'])
        ->source->toBe('Google Books + Open Library');

    Storage::disk('covers')->assertExists($metadata['cover_path']);
});
