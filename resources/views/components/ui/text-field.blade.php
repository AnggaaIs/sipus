@props(['name', 'label', 'type' => 'text', 'placeholder' => null, 'value' => null])

@php
    $isPassword = $type === 'password';
@endphp

<div class="space-y-2">
    <label for="{{ $name }}" class="text-sm font-medium text-card-foreground">
        {{ $label }}
    </label>

    @if ($isPassword)
        <div x-data="{ visible: false }" class="relative">
            <input id="{{ $name }}" name="{{ $name }}" type="password" value="{{ old($name, $value) }}"
                x-bind:type="visible ? 'text' : 'password'" placeholder="{{ $placeholder }}"
                {{ $attributes->class([
                    'block w-full rounded-[var(--radius)] border border-input bg-background px-3 py-3 pr-12 text-sm text-foreground outline-none transition focus:border-ring focus:ring-2 focus:ring-ring/20',
                ]) }}>

            <button type="button" x-on:click="visible = ! visible"
                x-bind:aria-label="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                x-bind:title="visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                class="absolute inset-y-0 right-0 inline-flex items-center justify-center px-3 text-muted-foreground transition-colors duration-200 hover:text-foreground">
                <x-ui.icon name="eye" x-show="! visible" aria-hidden="true" class="h-5 w-5" />
                <x-ui.icon name="eye-slash" x-cloak x-show="visible" aria-hidden="true" class="h-5 w-5" />
            </button>
        </div>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
            {{ $attributes->class([
                'block w-full rounded-[var(--radius)] border border-input bg-background px-3 py-3 text-sm text-foreground outline-none transition focus:border-ring focus:ring-2 focus:ring-ring/20',
            ]) }}>
    @endif

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
