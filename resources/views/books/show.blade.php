<x-layouts.guest :title="$book->title . ' - SIPUS'" :description="filled($book->description)
    ? \Illuminate\Support\Str::limit(strip_tags($book->description), 160)
    : 'Lihat detail buku ' . $book->title . ' beserta penulis, kategori, dan ketersediaannya di SIPUS.'">
    @php
        $authorNames = $book->authors->pluck('name')->join(', ');
        $categoryColor = filled($book->category?->color) ? $book->category->color : 'var(--primary)';
        $availPercent = $book->total_copies > 0 ? round(($book->available_copies / $book->total_copies) * 100) : 0;
        $isAvailable = $book->available_copies > 0;
    @endphp

    <section class="bg-[var(--background)] px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl space-y-8">
            <a href="{{ route('books.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-[var(--muted-foreground)] transition-colors duration-200 hover:text-[var(--foreground)]">
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke katalog</span>
            </a>

            <div class="grid gap-8 lg:grid-cols-[340px_minmax(0,1fr)] lg:gap-10">

                {{-- Kolom kiri: Cover + Ketersediaan --}}
                <div class="lg:sticky lg:top-8 lg:self-start lg:max-h-[calc(100vh-4rem)]">
                    <x-ui.card padding="none" class="overflow-hidden">
                        {{-- Cover --}}
                        @if ($book->cover_url)
                            <div class="w-full bg-[var(--muted)]" style="height: clamp(320px, 55vh, 520px)">
                                <img src="{{ $book->cover_url }}" alt="Sampul buku {{ $book->title }}"
                                    class="h-full w-full object-contain">
                            </div>
                        @else
                            <div class="flex w-full flex-col items-center justify-center gap-2 bg-[var(--card)]"
                                style="height: clamp(320px, 55vh, 520px)">
                                <img src="{{ asset('images/sepang_sma_logo.png') }}" alt="Logo SMA Semen Padang"
                                    class="h-16 w-16 opacity-70" />
                                <span
                                    class="text-[11px] font-semibold uppercase tracking-[0.12em] text-[var(--muted-foreground)]">
                                    No cover
                                </span>
                            </div>
                        @endif

                        {{-- Bar Ketersediaan --}}
                        <div class="border-t border-[var(--border)] px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-full {{ $isAvailable ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)]">
                                            Ketersediaan</p>
                                        <p class="text-sm font-medium text-[var(--foreground)]">
                                            {{ $book->available_copies }} dari {{ $book->total_copies }} eksemplar
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $isAvailable ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $isAvailable ? 'Tersedia' : 'Dipinjam' }}
                                </span>
                            </div>

                            {{-- Progress bar --}}
                            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-[var(--border)]">
                                <div class="h-full rounded-full transition-all duration-500 {{ $isAvailable ? 'bg-emerald-500' : 'bg-rose-500' }}"
                                    style="width: {{ $availPercent }}%">
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Kolom kanan --}}
                <div class="space-y-6">
                    <div class="space-y-3">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium text-[var(--foreground)]"
                            style="background-color: color-mix(in oklab, {{ $categoryColor }} 14%, white);">
                            {{ $book->category?->name ?? 'Umum' }}
                        </span>

                        <div class="space-y-2">
                            <h1 class="text-3xl font-semibold tracking-tight text-[var(--foreground)] sm:text-4xl">
                                {{ $book->title }}
                            </h1>
                            <p class="text-base leading-7 text-[var(--muted-foreground)]">
                                {{ $authorNames !== '' ? $authorNames : 'Penulis belum tersedia' }}
                            </p>
                        </div>
                    </div>

                    {{-- Meta grid (tanpa ketersediaan) --}}
                    <x-ui.card padding="sm">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <x-ui.meta-item icon="building-library" label="Penerbit" :value="$book->publisher?->name ?? '-'" />
                            <x-ui.meta-item icon="calendar-days" label="Tahun terbit" :value="$book->publish_year ?? '-'" />
                            <x-ui.meta-item icon="document-text" label="Jumlah halaman" :value="$book->pages ? $book->pages . ' halaman' : '-'" />
                            <x-ui.meta-item icon="language" label="Bahasa" :value="strtoupper($book->language)" />
                            <x-ui.meta-item icon="identification" label="ISBN" :value="$book->isbn ?? '-'" />
                            <x-ui.meta-item icon="bookmark" label="DDC" :value="$book->ddc ? $book->ddc->code . ' - ' . $book->ddc->name : '-'" />
                        </div>
                    </x-ui.card>

                    {{-- Deskripsi --}}
                    <x-ui.card padding="sm">
                        <div class="space-y-3">
                            <h2 class="text-xl font-semibold text-[var(--foreground)]">Tentang buku ini</h2>
                            <p class="text-base leading-7 text-[var(--muted-foreground)]">
                                {{ $book->description ?: 'Deskripsi buku belum tersedia.' }}
                            </p>
                        </div>
                    </x-ui.card>

                    {{-- Info peminjaman --}}
                    <x-ui.card padding="sm">
                        <div class="flex gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[var(--radius)] bg-[color-mix(in_oklab,var(--primary)_12%,white)] text-[var(--primary)]">
                                <x-ui.icon name="building-library" class="h-5 w-5" />
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-base font-semibold text-[var(--foreground)]">Peminjaman langsung di
                                    perpustakaan</h2>
                                <p class="text-sm leading-6 text-[var(--muted-foreground)]">
                                    Untuk meminjam buku ini, silakan datang langsung ke perpustakaan SMA Semen Padang.
                                    Gunakan SIPUS untuk mengecek informasi dan ketersediaan buku sebelum datang.
                                </p>
                            </div>
                        </div>
                    </x-ui.card>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <x-ui.button :href="route('books.index')" variant="ghost" size="lg">
                            <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                            Lihat buku lain
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.guest>
