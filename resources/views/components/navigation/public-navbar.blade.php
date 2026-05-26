@props([
    'brand' => config('app.name', 'SIPUS'),
    'links' => [],
    'loginUrl',
    'overlay' => false,
])

<nav
    x-data="publicNavbar({ overlay: @js($overlay) })"
    x-init="syncScrollState()"
    x-on:scroll.window="syncScrollState()"
    x-bind:class="{
        'fixed inset-x-0 top-0': overlay,
        'sticky top-0': !overlay,
        'border-transparent bg-transparent text-white [text-shadow:0_1px_10px_rgba(0,0,0,0.28)]': overlay && !isScrolled,
        'border-b border-border/80 bg-background/82 text-foreground backdrop-blur-xl supports-[backdrop-filter]:bg-background/70': !overlay || isScrolled,
    }"
    {{ $attributes->merge(['class' => 'z-40 transition-[background-color,border-color,color,box-shadow,backdrop-filter] duration-300']) }}>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4 sm:min-h-18 sm:gap-6">
            <a href="{{ url('/') }}"
                x-bind:class="overlay && !isScrolled ? '[&_.brand-title]:!text-white [&_.brand-subtitle]:!text-white/88' : ''">
                <x-navigation.brand :title="$brand" />
            </a>

            <div class="hidden items-center gap-3 md:flex">
                @foreach ($links as $link)
                    @php
                        $isActive = (bool) ($link['active'] ?? false);
                    @endphp

                    <a href="{{ $link['href'] }}" @if ($isActive) aria-current="page" @endif
                        @class([
                            'relative inline-flex items-center rounded-[calc(var(--radius)-0.125rem)] px-3 py-2 text-sm font-medium transition-all duration-200 after:absolute after:right-3 after:bottom-1.5 after:left-3 after:h-px after:origin-left after:opacity-90 after:transition-transform after:duration-200',
                            'bg-[color-mix(in_oklab,var(--color-primary)_10%,white)] text-primary after:scale-x-100 after:bg-primary' => $isActive,
                            'text-foreground/70 hover:-translate-y-px hover:bg-foreground/8 hover:text-foreground after:scale-x-0 after:bg-primary hover:after:scale-x-100' => ! $isActive,
                        ])
                        x-bind:class="overlay && !isScrolled
                            ? '{{ $isActive ? '!bg-black/28 !text-white shadow-[0_10px_24px_-12px_rgba(0,0,0,0.65)] after:scale-x-100 after:!bg-white' : '!text-white after:scale-x-0 after:!bg-white hover:-translate-y-px hover:!bg-black/28 hover:!text-white hover:after:scale-x-100' }}'
                            : ''">
                        {{ $link['label'] }}
                    </a>
                @endforeach

                @php
                    $currentUser = auth()->user();
                @endphp

                @if ($currentUser)
                    <x-ui.button :href="$currentUser->role === 'admin' ? url('/admin') : url('/user')" variant="primary" size="md">
                        <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                        Dasbor
                    </x-ui.button>
                @else
                    <x-ui.button :href="$loginUrl" variant="primary" size="md">
                        <x-ui.icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                        Masuk
                    </x-ui.button>
                @endif
            </div>

            <x-navigation.mobile-sheet :links="$links" :login-url="$loginUrl" :brand="$brand" :overlay="$overlay" />
        </div>
    </div>
</nav>
