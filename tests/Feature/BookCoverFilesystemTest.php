<?php

use App\Models\Book;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

test('the covers disk is configured for publicly accessible book cover uploads', function () {
    expect(config('filesystems.disks.covers'))
        ->toMatchArray([
            'driver' => 'local',
            'root' => storage_path('app/public/covers'),
            'url' => rtrim((string) config('app.url'), '/').'/storage/covers',
            'visibility' => 'public',
        ]);

    expect(Storage::disk('covers'))->toBeInstanceOf(FilesystemAdapter::class);
});

test('a stored book cover resolves to its public storage URL', function () {
    $book = new Book(['cover' => 'teknologi/laravel.jpg']);

    expect($book->cover_url)
        ->toBe(rtrim((string) config('app.url'), '/').'/storage/covers/teknologi/laravel.jpg');
});
