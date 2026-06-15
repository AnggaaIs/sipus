@props([
    'brand' => config('app.name', 'SIPUS'),
    'links' => [],
    'loginUrl' => url('/login'),
    'overlay' => false,
])

@php
    $brandName = is_array($brand) ? $brand['name'] ?? 'SIPUS' : $brand;
    $brandSubtitle = is_array($brand)
        ? $brand['subtitle'] ?? 'Sistem Informasi Perpustakaan'
        : 'Sistem Informasi Perpustakaan';
    $brandUrl = is_array($brand) ? $brand['url'] ?? url('/') : url('/');

    $navId = 'mobile-menu-' . uniqid();
    $currentUser = auth()->user();
    $dashboardUrl = $currentUser?->role === 'admin' ? url('/admin') : url('/user');

    $navClass = $overlay
        ? 'absolute inset-x-0 top-0 z-50 border-b border-white/10 bg-white/10 backdrop-blur-md'
        : 'sticky top-0 z-50 border-b border-border bg-white/85 backdrop-blur-xl';

    $desktopLinkClass = $overlay
        ? 'text-white/80 hover:text-white'
        : 'text-muted-foreground hover:text-foreground';

    $desktopActiveClass = $overlay ? 'text-white' : 'text-primary';

    $mobileMenuClass = $overlay
        ? 'border-white/10 bg-slate-950/95 text-white'
        : 'border-border bg-white text-foreground';

    $mobileLinkClass = $overlay ? 'hover:bg-white/10' : 'text-muted-foreground hover:bg-accent hover:text-foreground';

    $mobileActiveClass = $overlay
        ? 'bg-white/10 text-white'
        : 'bg-[color-mix(in_oklab,var(--color-primary)_10%,white)] text-primary';
@endphp

<nav class="{{ $navClass }}">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ $brandUrl }}" @class([
            'min-w-0',
            '[&_.brand-title]:text-white [&_.brand-subtitle]:text-white/70' => $overlay,
        ])>
            <x-navigation.brand :title="$brandName" :subtitle="$brandSubtitle" />
        </a>

        <div class="hidden items-center gap-8 md:flex">
            @foreach ($links as $link)
                @php
                    $href = $link['href'] ?? ($link['url'] ?? '#');
                    $isActive = (bool) ($link['active'] ?? false);
                @endphp

                <a href="{{ $href }}" @if ($isActive) aria-current="page" @endif
                    class="text-sm font-medium transition-colors duration-200 {{ $isActive ? $desktopActiveClass : $desktopLinkClass }}">
                    {{ $link['label'] ?? ($link['title'] ?? 'Menu') }}
                </a>
            @endforeach

            @if ($currentUser)
                <x-ui.button :href="$dashboardUrl" variant="primary" size="md">
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

        <x-ui.button variant="ghost" size="icon" data-mobile-menu-button data-target="{{ $navId }}"
            aria-controls="{{ $navId }}" aria-expanded="false" aria-label="Buka menu navigasi" @class([
                'md:hidden',
                'border-white/15 bg-white/10 text-white hover:border-white/35 hover:bg-white/15 hover:text-white' => $overlay,
            ])>
            <span class="sr-only">Buka menu</span>
            <x-ui.icon name="bars-3" data-menu-icon class="h-5 w-5" />
            <x-ui.icon name="x-mark" data-close-icon class="hidden h-5 w-5" />
        </x-ui.button>
    </div>

    <div id="{{ $navId }}" data-mobile-menu class="hidden border-t {{ $mobileMenuClass }} md:hidden">
        <div class="space-y-1 px-4 py-4">
            @foreach ($links as $link)
                @php
                    $href = $link['href'] ?? ($link['url'] ?? '#');
                    $isActive = (bool) ($link['active'] ?? false);
                @endphp

                <a href="{{ $href }}" data-mobile-menu-link @if ($isActive) aria-current="page" @endif
                    class="block rounded-xl px-4 py-3 text-sm font-medium transition {{ $isActive ? $mobileActiveClass : $mobileLinkClass }}">
                    {{ $link['label'] ?? ($link['title'] ?? 'Menu') }}
                </a>
            @endforeach

            <div class="pt-3">
                @if ($currentUser)
                    <x-ui.button :href="$dashboardUrl" variant="primary" size="lg" full-width data-mobile-menu-link>
                        <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                        Dasbor
                    </x-ui.button>
                @else
                    <x-ui.button :href="$loginUrl" variant="primary" size="lg" full-width data-mobile-menu-link>
                        <x-ui.icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                        Masuk
                    </x-ui.button>
                @endif
            </div>
        </div>
    </div>
</nav>

@once
    <script>
        document.addEventListener('click', function(event) {
            const button = event.target.closest('[data-mobile-menu-button]');

            if (button) {
                const menu = document.getElementById(button.dataset.target);

                if (!menu) return;

                const menuIcon = button.querySelector('[data-menu-icon]');
                const closeIcon = button.querySelector('[data-close-icon]');
                const isOpen = !menu.classList.contains('hidden');

                menu.classList.toggle('hidden', isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));

                menuIcon?.classList.toggle('hidden', !isOpen);
                closeIcon?.classList.toggle('hidden', isOpen);
            }

            const link = event.target.closest('[data-mobile-menu-link]');

            if (link) {
                const menu = link.closest('[data-mobile-menu]');

                if (!menu) return;

                const button = document.querySelector(`[data-mobile-menu-button][data-target="${menu.id}"]`);

                menu.classList.add('hidden');

                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                    button.querySelector('[data-menu-icon]')?.classList.remove('hidden');
                    button.querySelector('[data-close-icon]')?.classList.add('hidden');
                }
            }
        });
    </script>
@endonce
