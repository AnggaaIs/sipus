<?php

use App\Models\User;

test('user dashboard shows richer overview widgets and charts', function () {
    $member = User::factory()->member()->make();

    $response = $this
        ->actingAs($member)
        ->get(route('filament.user.pages.dashboard'));

    $response->assertOk()
        ->assertSee('Ringkasan Aktivitas Saya')
        ->assertSee('Pinjaman Aktif Saya')
        ->assertSee('Peminjaman Bulan Ini')
        ->assertSee('Riwayat Pengembalian')
        ->assertSee('Denda Belum Lunas')
        ->assertSee('Aktivitas Peminjaman 6 Bulan Terakhir')
        ->assertSee('Komposisi Status Peminjaman');
});
