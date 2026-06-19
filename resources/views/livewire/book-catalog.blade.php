<div>
    {{-- Bagian judul --}}
    <section class="border-b border-[var(--border)] bg-[var(--card)] px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-8">
            <div class="max-w-3xl space-y-3">
                <div class="flex items-center gap-2 text-sm font-medium text-[var(--primary)]">
                    <x-ui.icon name="book-open" class="h-4 w-4" />
                    <p>Katalog Buku</p>
                </div>
                <h1 class="text-3xl font-semibold tracking-tight text-[var(--foreground)] sm:text-4xl">
                    Lihat semua koleksi buku yang tersedia di SIPUS.
                </h1>
                <p class="text-base leading-7 text-[var(--muted-foreground)]">
                    Cari berdasarkan judul buku, nama penulis, atau ISBN untuk menemukan buku yang Anda butuhkan.
                </p>
            </div>

            <x-ui.card padding="sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <div class="flex-1">
                        <label for="catalog-query" class="sr-only">Cari buku</label>
                        <div class="flex items-center gap-3 rounded-(--radius) border border-(--input) bg-(--background) px-4 py-3">
                            <x-ui.icon name="magnifying-glass" class="h-5 w-5 text-(--muted-foreground)" />
                            <input
                                id="catalog-query"
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Cari judul buku, penulis, ISBN, atau lokasi..."
                                class="w-full bg-transparent text-sm text-(--foreground) outline-none placeholder:text-(--muted-foreground)"
                            >
                        </div>
                    </div>

                    <div class="lg:w-64">
                        <label for="catalog-ddc" class="sr-only">Filter DDC</label>
                        <div
                            class="flex items-center gap-3 rounded-[var(--radius)] border border-[var(--input)] bg-[var(--background)] px-4 py-3">
                            <x-ui.icon name="building-library" class="h-5 w-5 text-[var(--muted-foreground)]" />
                            <select id="catalog-ddc" wire:model.live="ddcId"
                                class="w-full bg-transparent text-sm text-[var(--foreground)] outline-none">
                                <option value="">Semua DDC</option>
                                @foreach ($ddcs as $ddc)
                                    <option value="{{ $ddc->id }}">
                                        {{ $ddc->code }} - {{ $ddc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row items-center">
                        @if ($search !== '' || $ddcId !== null)
                            <x-ui.button wire:click="$set('search', ''); $set('ddcId', null);" variant="ghost" size="lg">
                                <x-ui.icon name="arrow-path" class="h-4 w-4" />
                                Bersihkan
                            </x-ui.button>
                        @endif
                        
                        <div wire:loading class="text-sm font-medium text-[var(--muted-foreground)] lg:ml-2">
                            Memuat...
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </section>

    {{-- Daftar buku --}}
    <section class="bg-[var(--background)] px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-1">
                    <h2 class="text-2xl font-semibold tracking-tight text-[var(--foreground)]">
                        {{ $search !== '' || $ddcId !== null ? 'Hasil pencarian buku' : 'Semua buku' }}
                    </h2>
                    <p class="text-sm leading-6 text-[var(--muted-foreground)]">
                        @if ($books->total() > 0)
                            Menampilkan {{ $books->firstItem() }}&ndash;{{ $books->lastItem() }} dari
                            {{ $books->total() }} buku.
                        @else
                            Belum ada buku yang bisa ditampilkan.
                        @endif
                    </p>
                </div>

                @if ($search !== '')
                    <p class="text-sm text-[var(--muted-foreground)]">
                        Hasil untuk <span
                            class="font-semibold text-[var(--foreground)]">&ldquo;{{ $search }}&rdquo;</span>
                    </p>
                @endif
            </div>

            @if ($books->count() > 0)
                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-5 relative">
                    {{-- Efek loading di atas buku saat fetch data baru --}}
                    <div wire:loading class="absolute inset-0 z-10 bg-(--background)/50 rounded-xl transition-all"></div>
                    
                    @foreach ($books as $book)
                        <x-books.book-card :book="$book" meta-label="Ketersediaan" :meta-value="$book->available_copies . ' dari ' . $book->total_copies . ' buku'" />
                    @endforeach
                </div>

                @if ($books->hasPages())
                    <div class="mt-8 border-t border-[var(--border)] pt-8">
                        {{ $books->onEachSide(1)->links() }}
                    </div>
                @endif
            @else
                <x-ui.card>
                    <div class="space-y-3 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[var(--muted)]">
                            <x-ui.icon name="magnifying-glass" class="h-5 w-5 text-[var(--muted-foreground)]" />
                        </div>
                        <h3 class="text-xl font-semibold text-[var(--foreground)]">Buku tidak ditemukan</h3>
                        <p class="text-sm leading-6 text-[var(--muted-foreground)]">
                            Coba gunakan kata kunci lain, atau bersihkan filter.
                        </p>
                        <div class="pt-1">
                            <x-ui.button wire:click="$set('search', ''); $set('ddcId', null);" variant="ghost" size="lg">
                                <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                                Lihat semua buku
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </section>
</div>