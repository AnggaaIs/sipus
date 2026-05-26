@props(['name', 'label', 'value' => '1', 'checked' => false])

<label {{ $attributes->class(['flex items-center gap-3 text-[var(--muted-foreground)]']) }}>
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old($name, $checked))
        class="h-4 w-4 rounded border-[var(--input)] text-[var(--primary)] focus:ring-[var(--ring)]">
    <span>{{ $label }}</span>
</label>
