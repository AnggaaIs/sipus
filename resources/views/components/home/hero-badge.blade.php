@props([
    'icon',
    'label',
])

<div {{ $attributes->class(['inline-flex items-center gap-2 rounded-full border border-white/18 bg-white/10 px-3 py-2 text-sm font-medium text-white/88 backdrop-blur']) }}>
    <x-ui.icon :name="$icon" class="h-4 w-4" />
    <span>{{ $label }}</span>
</div>
