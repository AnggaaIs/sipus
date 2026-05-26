@props([
    'eyebrow' => null,
    'icon' => null,
    'title',
    'description' => null,
])

<div {{ $attributes->class(['max-w-2xl space-y-3']) }}>
    @if ($eyebrow)
        <div class="flex items-center gap-2 text-sm font-medium text-primary">
            @if ($icon)
                <x-ui.icon :name="$icon" class="h-4 w-4" />
            @endif

            <p>{{ $eyebrow }}</p>
        </div>
    @endif

    <h2 class="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
        {{ $title }}
    </h2>

    @if ($description)
        <p class="text-base leading-7 text-muted-foreground">
            {{ $description }}
        </p>
    @endif
</div>
