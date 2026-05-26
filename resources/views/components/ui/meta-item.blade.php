@props([
    'icon',
    'label',
    'value',
])

<div {{ $attributes->class(['flex items-start gap-3 rounded-[calc(var(--radius)+0.125rem)] border border-[var(--border)] bg-[var(--background)] p-4']) }}>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] bg-[color:var(--primary)]/8 text-[var(--primary)]">
        <x-ui.icon :name="$icon" class="h-5 w-5" />
    </span>

    <div class="space-y-1">
        <p class="text-sm font-medium text-[var(--muted-foreground)]">{{ $label }}</p>
        <p class="text-base font-semibold text-[var(--foreground)]">{{ $value }}</p>
    </div>
</div>
