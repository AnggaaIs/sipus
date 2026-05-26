<?php

use Illuminate\Support\Collection;

test('home page uses overlay navbar presentation', function () {
    $html = view('welcome', [
        'latestBooks' => Collection::make(),
        'mostBorrowedBooks' => Collection::make(),
    ])->render();

    expect($html)->toContain('publicNavbar({ overlay: true })');
    expect($html)->toContain('bg-background');
    expect($html)->toContain('text-muted-foreground');
    expect($html)->toContain('<meta name="description" content="SIPUS memudahkan Anda menjelajahi katalog buku, menemukan koleksi terbaru, dan melihat buku yang paling sering dipinjam di perpustakaan.">');
    expect($html)->toContain('<meta property="og:title" content="SIPUS - Sistem Informasi Perpustakaan">');
    expect($html)->toContain('<link rel="canonical" href="http://localhost:8000">');
});
