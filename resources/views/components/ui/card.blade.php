@props([
    'padding' => 'md',
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4 sm:p-5',
        'md' => 'p-6 sm:p-8',
        'lg' => 'p-8 sm:p-10',
    ][$padding] ?? 'p-6 sm:p-8';
@endphp

<div {{ $attributes->class([
    'rounded-[calc(var(--radius)+0.75rem)] border border-border bg-card',
    $paddingClasses,
]) }}>
    {{ $slot }}
</div>