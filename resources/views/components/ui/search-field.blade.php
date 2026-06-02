@props([
    'id',
    'name' => 'q',
    'value' => null,
    'placeholder' => 'Cari...',
])

<div {{ $attributes->class(['flex items-center gap-3 rounded-[var(--radius)] border border-input bg-background px-4 py-3']) }}>
    <x-ui.icon name="magnifying-glass" class="h-5 w-5 text-muted-foreground" />

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="text"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="w-full bg-transparent text-sm text-foreground outline-none placeholder:text-muted-foreground"
    >
</div>