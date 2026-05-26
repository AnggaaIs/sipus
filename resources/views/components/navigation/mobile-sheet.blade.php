@props([
    'links' => [],
    'loginUrl',
    'brand' => config('app.name', 'SIPUS'),
    'overlay' => false,
])

<div class="md:hidden" data-sheet-root>
    <div x-data="mobileSheet" x-on:keydown.escape.window="closeSheet()">
        @if ($overlay)
            <x-ui.button variant="ghost" size="icon" x-on:click="openSheet()" aria-label="Buka menu navigasi"
                x-bind:class="!isScrolled ? 'border-white/15 bg-white/10 text-white hover:border-white/35 hover:bg-white/16 hover:text-white' : ''">
                <span class="sr-only">Buka menu</span>
                <x-ui.icon name="bars-3" class="h-5 w-5" />
            </x-ui.button>
        @else
            <x-ui.button variant="ghost" size="icon" x-on:click="openSheet()" aria-label="Buka menu navigasi">
                <span class="sr-only">Buka menu</span>
                <x-ui.icon name="bars-3" class="h-5 w-5" />
            </x-ui.button>
        @endif

        <div x-cloak x-show="isOpen" x-on:click="closeSheet()" x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-stone-950/30"></div>

        <section x-cloak x-show="isOpen" x-on:click.outside="closeSheet()"
            x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 z-50 flex h-dvh w-[88vw] max-w-sm flex-col border-l border-border bg-white text-card-foreground">
            <div class="flex items-start justify-between gap-3 border-b border-border bg-white px-4 py-4">
                <x-navigation.brand :title="$brand" compact class="min-w-0 flex-1" />

                <x-ui.button variant="ghost" size="icon-sm" x-on:click="closeSheet()" aria-label="Tutup menu">
                    <x-ui.icon name="x-mark" class="h-5 w-5" />
                </x-ui.button>
            </div>

            <div class="flex flex-1 flex-col justify-between overflow-y-auto bg-white px-4 py-5">
                <nav class="flex flex-col gap-1.5">
                    @foreach ($links as $link)
                        @php
                            $isActive = (bool) ($link['active'] ?? false);
                        @endphp

                        <a href="{{ $link['href'] }}" x-on:click="closeSheet()"
                            @if ($isActive) aria-current="page" @endif @class([
                                'rounded-[calc(var(--radius)-0.125rem)] px-3 py-3 text-base font-medium transition-colors duration-200',
                                'bg-[color-mix(in_oklab,var(--color-primary)_10%,white)] text-primary' => $isActive,
                                'text-muted-foreground hover:bg-accent hover:text-foreground' => !$isActive,
                            ])>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-border pt-5">
                    @php
                        $currentUser = auth()->user();
                    @endphp

                    @if ($currentUser)
                        <x-ui.button :href="$currentUser->role === 'admin' ? url('/admin') : url('/user')" variant="primary" size="lg" full-width
                            x-on:click="closeSheet()">
                            <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                            Dasbor
                        </x-ui.button>
                    @else
                        <x-ui.button :href="$loginUrl" variant="primary" size="lg" full-width
                            x-on:click="closeSheet()">
                            <x-ui.icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                            Masuk
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
