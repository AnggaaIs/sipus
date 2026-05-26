@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'fullWidth' => false,
])

@php
    $variantClasses = [
        'primary' => 'rounded-[var(--radius)] border-primary bg-primary text-primary-foreground hover:bg-secondary hover:text-secondary-foreground',
        'ghost' => 'rounded-[var(--radius)] border-input text-muted-foreground hover:border-foreground hover:bg-accent hover:text-foreground',
    ][$variant] ?? 'rounded-[var(--radius)] border-primary bg-primary text-primary-foreground hover:bg-secondary hover:text-secondary-foreground';

    $sizeClasses = [
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-4 py-3 text-sm',
        'icon' => 'h-11 w-11 p-0 text-sm',
        'icon-sm' => 'h-10 w-10 p-0 text-base',
    ][$size] ?? 'px-4 py-2 text-sm';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 border font-semibold transition-colors duration-200',
        $variantClasses,
        $sizeClasses,
        'w-full' => $fullWidth,
    ]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 border font-semibold transition-colors duration-200',
        $variantClasses,
        $sizeClasses,
        'w-full' => $fullWidth,
    ])->merge(['type' => $type]) }}>
        {{ $slot }}
    </button>
@endif
