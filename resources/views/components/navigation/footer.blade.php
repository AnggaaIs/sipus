@props([
    'brand' => config('app.name', 'SIPUS'),
    'links' => [],
    'loginUrl',
])

<footer class="border-t border-border bg-card">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.45fr)_minmax(180px,0.7fr)_auto]">
            <div class="max-w-xl space-y-3">
                <x-navigation.brand :title="$brand" compact />
                <p class="text-sm leading-6 text-muted-foreground">
                    Tempat yang lebih mudah untuk melihat koleksi buku dan masuk ke layanan perpustakaan SMA Semen
                    Padang.
                </p>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-semibold text-foreground">Navigasi</p>
                <div class="flex flex-col gap-2">
                    @foreach ($links as $link)
                        <a href="{{ $link['href'] }}"
                            class="text-sm text-muted-foreground transition-colors duration-200 hover:text-foreground">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-semibold text-foreground">Akses</p>
                @php
                    $currentUser = auth()->user();
                @endphp

                @if ($currentUser)
                    <x-ui.button :href="$currentUser->role === 'admin' ? url('/admin') : url('/user')" variant="ghost" size="md">
                        <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                        Dasbor
                    </x-ui.button>
                @else
                    <x-ui.button :href="$loginUrl" variant="ghost" size="md">
                        <x-ui.icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                        Masuk
                    </x-ui.button>
                @endif
            </div>
        </div>

        <div
            class="flex flex-col gap-2 border-t border-border pt-4 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $brand }}. Semua hak dilindungi.</p>
            <p>Sistem Informasi Perpustakaan SMA Semen Padang</p>
        </div>
    </div>
</footer>
