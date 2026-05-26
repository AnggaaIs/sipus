<?php

test('forgot password page can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertSuccessful()
        ->assertSee('Kirim tautan atur ulang kata sandi')
        ->assertSee('Kembali ke halaman masuk');
});

test('reset password page can be rendered', function () {
    $response = $this->get(route('password.reset', ['token' => 'sample-token']));

    $response->assertSuccessful()
        ->assertSee('Buat kata sandi baru')
        ->assertSee('Simpan kata sandi baru');
});
