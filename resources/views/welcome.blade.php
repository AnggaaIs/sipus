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
                    href="#"
                    variant="primary"
                    size="lg"
                    class=" text-white hover:bg-white hover:text-primary">
                    Jelajahi Katalog
                </x-ui.button>

                    <x-ui.button
                        href="#"
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
                    action="#"
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
    <section class="border-y border-border bg-card px-4 py-20 sm:px-6 lg:px-8">

        <div class="mx-auto max-w-7xl">

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-card-foreground">
                    Buku Terbaru
                </h2>

                <p class="mt-2 text-muted-foreground">
                    Koleksi yang baru ditambahkan ke perpustakaan.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

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
                    href="#"
                    variant="primary"
                    size="lg">
                    Mulai Sekarang
                </x-ui.button>

            </div>

        </div>

    </section>

</x-layouts.guest>