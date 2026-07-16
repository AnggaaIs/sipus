@props([
    'title' => config('app.name', 'SIPUS'),
    'brand' => config('app.name', 'SIPUS'),
    'description' =>
        'SIPUS adalah sistem informasi perpustakaan untuk menjelajahi katalog buku, kategori, dan detail koleksi perpustakaan dengan lebih mudah.',
    'robots' => 'index,follow',
    'canonical' => null,
    'image' => asset('images/library_image.jpg'),
    'type' => 'website',
    'navbarOverlay' => false,

    'navigationLinks' => [
        ['label' => 'Katalog', 'href' => route('books.index'), 'active' => request()->routeIs('books.*')],
        ['label' => 'Kategori', 'href' => route('categories.index'), 'active' => request()->routeIs('categories.*')],
        ['label' => 'Tentang', 'href' => url('/#tentang'), 'active' => false],
    ],

    'loginUrl' => route('login'),
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/progress.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    <div class="flex min-h-screen flex-col">

        {{-- Navbar --}}
        <x-navigation.navbar :brand="$brand" :links="$navigationLinks" :login-url="$loginUrl" :overlay="$navbarOverlay" />

        {{-- Content --}}
        <main class="flex-1">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <x-navigation.footer :brand="$brand" :links="$navigationLinks" :login-url="$loginUrl" />

    </div>

</body>

</html>
