@props([
    'title' => config('app.name', 'SIPUS'),
    'description' => 'Halaman autentikasi SIPUS untuk masuk, mendaftar, atau mengatur ulang akses akun perpustakaan.',
    'robots' => 'noindex,nofollow',
    'canonical' => null,
    'image' => asset('images/sepang_sma_logo.png'),
    'type' => 'website',
])

@php
    $metaDescription = trim($description);
    $metaCanonical = $canonical ?: url()->current();
    $metaImage = $image;
    $metaType = $type;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robots }}">

    <link rel="canonical" href="{{ $metaCanonical }}">

    <link rel="icon" type="image/png" href="{{ asset('images/sepang_sma_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/sepang_sma_logo.png') }}">

    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:site_name" content="{{ config('app.name', 'SIPUS') }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaCanonical }}">
    <meta property="og:image" content="{{ $metaImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background font-sans text-foreground antialiased">
    {{ $slot }}
</body>

</html>