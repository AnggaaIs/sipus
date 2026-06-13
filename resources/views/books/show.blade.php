<x-layouts.guest title="{{ $book->title }} - SIPUS">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-foreground">{{ $book->title }}</h1>
                <p class="mt-2 text-sm text-muted-foreground">Detail buku dan informasi koleksi.</p>
            </div>
            <a href="{{ route('books.index') }}" class="rounded-lg border border-border bg-white px-4 py-2 text-sm font-medium text-foreground transition hover:bg-slate-50">
                Kembali ke katalog
            </a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[320px_1fr]">
            <div class="rounded-3xl border border-border bg-card p-4 shadow-sm">
                <div class="overflow-hidden rounded-3xl bg-slate-100">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="Sampul {{ $book->title }}" class="h-[450px] w-full object-cover" />
                    @else
                        <div class="flex h-[450px] items-center justify-center bg-slate-200 text-center text-sm text-muted-foreground">
                            Tidak ada sampul tersedia.
                        </div>
                    @endif
                </div>
                <div class="mt-4 space-y-3 text-sm text-foreground">
                    <div>
                        <p class="font-semibold">Jumlah tersedia</p>
                        <p>{{ $book->available_copies }} dari {{ $book->total_copies }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Bahasa</p>
                        <p>{{ $book->language ?? 'Tidak tersedia' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Tahun terbit</p>
                        <p>{{ $book->publish_year ?? 'Tidak tersedia' }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6 rounded-3xl border border-border bg-card p-8 shadow-sm">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">Informasi Buku</h2>
                        <p class="mt-2 text-sm text-muted-foreground">Semua detail yang tersedia tentang buku ini.</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-muted-foreground">Penulis</p>
                            <p class="mt-1 text-base text-foreground">
                                {{ $book->authors->pluck('name')->join(', ') ?: 'Tidak tersedia' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-muted-foreground">Kategori</p>
                            <p class="mt-1 text-base text-foreground">{{ $book->category?->name ?? 'Tidak tersedia' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-muted-foreground">Penerbit</p>
                            <p class="mt-1 text-base text-foreground">{{ $book->publisher?->name ?? 'Tidak tersedia' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-muted-foreground">DDC</p>
                            <p class="mt-1 text-base text-foreground">{{ $book->ddc?->code ? 'DDC ' . $book->ddc->code : 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.2em] text-muted-foreground">Deskripsi</p>
                    <p class="text-sm leading-7 text-foreground">{{ $book->description ?? 'Deskripsi buku belum ditambahkan.' }}</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.guest>
