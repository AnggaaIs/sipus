<x-layouts.guest title="SIPUS - Sistem Informasi Perpustakaan">

    {{-- HERO SECTION --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-black to-gray py-32">

        {{-- Background Image --}}
        <div class="absolute inset-0">
            <img
                src="{{ asset('images/Perpustakaan.jpg') }}"
                alt="Perpustakaan"
                class="h-full w-full object-cover opacity-30" />
        </div>

        {{-- Decorative Blur --}}
        <div class="absolute -top-40 right-0 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-6">

            <div class="max-w-3xl">

                <span
                    class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur">
                     Sistem Informasi Perpustakaan
                </span>

                <h1
                    class="mt-6 text-5xl font-bold tracking-tight text-white md:text-7xl">
                    SIPUS
                </h1>

                <p
                    class="mt-6 text-lg leading-8 text-white/90 md:text-xl">
                    Temukan koleksi buku terbaik, kelola peminjaman,
                    dan akses informasi literasi sekolah dengan mudah
                    melalui satu platform modern.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">

               <x-ui.button
                    :href="route('books.index')"
                    variant="primary"
                    size="lg"
                    class=" text-white hover:bg-white hover:text-primary">
                    Jelajahi Katalog
                </x-ui.button>

                    <x-ui.button
                        href="#tentang"
                        variant="ghost"
                        size="lg"
                        class="border-white text-white hover:bg-white hover:text-primary">
                        Pelajari Lebih Lanjut
                    </x-ui.button>

                </div>

            </div>

        </div>
    </section>

    {{-- SEARCH SECTION --}}
    <section class="relative z-20 -mt-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">

            <x-ui.card class="rounded-3xl border border-border shadow-xl">

                <form
                    method="GET"
                    action="{{ route('books.index') }}"
                    class="flex flex-col gap-4 sm:flex-row sm:items-center">

                    <div class="flex-1">
                        <x-ui.search-field
                            id="catalog-search"
                            name="search"
                            placeholder="Cari judul buku, penulis, atau ISBN..." />
                    </div>

                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="sm:min-w-40">
                        Cari Buku
                    </x-ui.button>

                </form>

            </x-ui.card>

        </div>
    </section>



    {{-- BUKU TERBARU --}}
    <section class="border-y border-border bg-card px-4 pb-20 pt-12 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-7xl">

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-card-foreground">
                    Buku Terbaru
                </h2>

                <p class="mt-2 text-muted-foreground">
                    Koleksi yang baru ditambahkan ke perpustakaan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">

                @forelse ($latestBooks ?? [] as $book)

                    <x-books.book-card
                        :book="$book"
                        meta-label="Ketersediaan"
                        :meta-value="$book->available_copies . ' dari ' . $book->total_copies . ' buku'" />

                @empty

                    <x-ui.card>
                        <p class="text-sm text-muted-foreground">
                            Belum ada buku terbaru.
                        </p>
                    </x-ui.card>

                @endforelse

            </div>

        </div>

    </section>

    {{-- BUKU PALING SERING DIPINJAM --}}
    <section class="bg-background px-4 py-20 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-7xl space-y-10">

            <x-home.section-heading
                eyebrow="Favorit Pembaca"
                icon="fire"
                title="Buku Paling Sering Dipinjam"
                description="Koleksi yang paling banyak beredar di perpustakaan dan paling sering dipilih pembaca." />

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">

                @forelse ($mostBorrowedBooks ?? [] as $book)
                    <x-books.book-card
                        :book="$book"
                        meta-label="Total dipinjam"
                        :meta-value="(($book->loan_items_sum_quantity ?? null) !== null ? number_format((int) $book->loan_items_sum_quantity) . ' kali' : $book->available_copies . ' salinan tersedia')" />
                @empty
                    <x-ui.card>
                        <p class="text-sm text-muted-foreground">
                            Belum ada data buku terpopuler.
                        </p>
                    </x-ui.card>
                @endforelse

            </div>

        </div>

    </section>

    {{-- TENTANG SIPUS --}}
    <section id="tentang" class="scroll-mt-24 border-y border-border bg-card px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-start">
            <x-home.section-heading
                eyebrow="Tentang SIPUS"
                icon="book-open"
                title="Perpustakaan sekolah, lebih mudah diakses."
                description="SIPUS adalah Sistem Informasi Perpustakaan SMA Semen Padang yang membantu siswa menemukan koleksi, memantau peminjaman, dan membangun kebiasaan membaca." />

            <div class="grid gap-6 sm:grid-cols-3">
                <div class="space-y-3 border-t-2 border-primary pt-4">
                    <x-ui.icon name="magnifying-glass" class="h-6 w-6 text-primary" />
                    <h3 class="text-lg font-semibold text-card-foreground">Cari koleksi</h3>
                    <p class="text-sm leading-6 text-muted-foreground">Temukan buku berdasarkan judul, penulis, kategori, atau ISBN.</p>
                </div>

                <div class="space-y-3 border-t-2 border-primary pt-4">
                    <x-ui.icon name="clipboard-document-check" class="h-6 w-6 text-primary" />
                    <h3 class="text-lg font-semibold text-card-foreground">Pantau peminjaman</h3>
                    <p class="text-sm leading-6 text-muted-foreground">Lihat status peminjaman, tanggal jatuh tempo, dan riwayat buku.</p>
                </div>

                <div class="space-y-3 border-t-2 border-primary pt-4">
                    <x-ui.icon name="academic-cap" class="h-6 w-6 text-primary" />
                    <h3 class="text-lg font-semibold text-card-foreground">Tumbuhkan literasi</h3>
                    <p class="text-sm leading-6 text-muted-foreground">Akses informasi perpustakaan dalam satu platform yang mudah digunakan.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-background px-4 py-24 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-4xl text-center">

            <h2 class="text-4xl font-bold text-foreground">
                Mulai Jelajahi Perpustakaan SMA Semen Padang
            </h2>

            <p class="mt-4 text-lg text-muted-foreground">
                Temukan berbagai koleksi buku dan tingkatkan
                budaya literasi bersama SIPUS.
            </p>

            <div class="mt-8">

                <x-ui.button
                    :href="route('books.index')"
                    variant="primary"
                    size="lg">
                    Mulai Sekarang
                </x-ui.button>

            </div>

        </div>

    </section>

</x-layouts.guest>
