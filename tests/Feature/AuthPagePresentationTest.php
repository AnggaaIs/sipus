<?php

test('login page uses shared theme utilities', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('bg-background');
    $response->assertSee('text-primary');
    $response->assertSee('text-muted-foreground');
    $response->assertSee('SIPUS');
    $response->assertSee('<meta name="robots" content="noindex,nofollow">', false);
    $response->assertSee('<meta property="og:title" content="Masuk - SIPUS">', false);
});

test('register page uses shared theme utilities', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('bg-background');
    $response->assertSee('text-primary');
    $response->assertSee('text-muted-foreground');
    $response->assertSee('SIPUS');
    $response->assertSee('<meta name="robots" content="noindex,nofollow">', false);
});

test('forgot password page uses shared theme utilities', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
    $response->assertSee('bg-background');
    $response->assertSee('text-primary');
    $response->assertSee('text-muted-foreground');
    $response->assertSee('<meta name="robots" content="noindex,nofollow">', false);
});

test('reset password page uses shared theme utilities', function () {
    $response = $this->get(route('password.reset', ['token' => 'example-token']));

    $response->assertOk();
    $response->assertSee('bg-background');
    $response->assertSee('text-primary');
    $response->assertSee('text-muted-foreground');
    $response->assertSee('<meta name="robots" content="noindex,nofollow">', false);
});
