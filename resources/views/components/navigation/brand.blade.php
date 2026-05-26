@props([
    'title' => config('app.name', 'SIPUS'),
    'subtitle' => 'Sistem Informasi Perpustakaan',
    'compact' => false,
    'showSubtitle' => true,
])

@php
    $imageClasses = $compact ? 'h-9 w-9' : 'h-9 w-9 sm:h-11 sm:w-11';
    $titleClasses = $compact
        ? 'text-sm tracking-[0.14em]'
        : 'text-sm tracking-[0.14em] sm:text-base sm:tracking-[0.18em]';
    $subtitleClasses = $compact
        ? 'text-[0.65rem] leading-tight tracking-[0.03em] max-w-[12rem]'
        : 'text-[0.68rem] leading-tight tracking-[0.03em] sm:text-xs sm:tracking-[0.08em] sm:uppercase';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <img src="{{ asset('images/sepang_sma_logo.png') }}" alt="Logo SMA Semen Padang" class="{{ $imageClasses }} object-contain">

    <div class="flex flex-col">
        <span class="brand-title text-foreground {{ $titleClasses }} font-semibold uppercase">
            {{ $title }}
        </span>
        @if ($showSubtitle)
            <span @class([
                'brand-subtitle font-medium text-foreground/72',
                $subtitleClasses,
                'uppercase' => ! $compact,
            ])>
                {{ $subtitle }}
            </span>
        @endif
    </div>
</div>
