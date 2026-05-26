<x-layouts.guest title="SIPUS - Sistem Informasi Perpustakaan"
    description="SIPUS memudahkan Anda menjelajahi katalog buku, menemukan koleksi terbaru, dan melihat buku yang paling sering dipinjam di perpustakaan."
    navbar-overlay>
    {{-- Bagian pembuka --}}
    <section id="beranda" class="relative overflow-hidden border-b border-border bg-background">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset('images/library_image.jpg') }}');"></div>
            <div class="absolute inset-x-0 top-0 h-32 bg-linear-to-b from-black/55 via-black/18 to-transparent"></div>
            <div
                class="absolute inset-0 bg-[linear-gradient(90deg,color-mix(in_oklab,var(--color-foreground)_75%,transparent)_0%,color-mix(in_oklab,var(--color-foreground)_58%,transparent)_42%,color-mix(in_oklab,var(--color-foreground)_18%,transparent)_100%)]">
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pb-24 pt-28 sm:px-6 sm:pb-28 sm:pt-32 lg:px-8 lg:pb-36 lg:pt-36">
            <div class="max-w-3xl space-y-7">
                {{-- Judul utama --}}
                <div class="space-y-4">
                    <h1
                        class="max-w-2xl text-4xl font-semibold leading-[1.15] tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Cari buku dan temukan koleksi perpustakaan dengan lebih mudah.
                    </h1>
                    <p class="max-w-xl text-base leading-7 text-white/75 sm:text-lg">
                        Mulai dari katalog, lihat buku terbaru, dan temukan buku yang sering dipinjam sebelum masuk ke
                        akun SIPUS.
                    </p>
                </div>

                {{-- Tombol aksi --}}
                <div class="flex flex-col gap-3 pt-1 sm:flex-row">
                    <x-ui.button :href="route('books.index')" variant="primary" size="lg"
                        class="shadow-lg shadow-blue-500/25 transition-all duration-200 hover:shadow-blue-500/40 hover:-translate-y-0.5">
                        <x-ui.icon name="book-open" class="h-4 w-4" />
                        Jelajahi katalog
                    </x-ui.button>
                    @guest
                        <x-ui.button :href="route('login')" variant="ghost" size="lg"
                            class="border-white/30 bg-white/10 text-white backdrop-blur-sm transition-all duration-200 hover:border-white/50 hover:bg-white/20 hover:text-white hover:-translate-y-0.5">
                            <x-ui.icon name="arrow-right-on-rectangle" class="h-4 w-4" />
                            Masuk
                        </x-ui.button>
                    @endguest

                    @auth
                        <x-ui.button :href="auth()->user()->role === 'admin' ? url('/admin') : url('/user')" variant="ghost" size="lg"
                            class="border-white/30 bg-white/10 text-white backdrop-blur-sm transition-all duration-200 hover:border-white/50 hover:bg-white/20 hover:text-white hover:-translate-y-0.5">
                            <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                            Dasbor
                        </x-ui.button>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Pencarian utama --}}
    <section id="katalog" class="relative z-10 -mt-10 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">
            <x-ui.card class="shadow-[0_24px_60px_-24px_color-mix(in_oklab,var(--color-foreground)_30%,transparent)]">
                <form method="GET" action="{{ route('books.index') }}"
                    class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex-1">
                        <label for="catalog-search" class="sr-only">Cari koleksi</label>
                        <x-ui.search-field id="catalog-search" name="search"
                            placeholder="Cari judul buku, penulis, atau ISBN..." />
                    </div>
                    <x-ui.button type="submit" variant="primary" size="lg" class="sm:min-w-32">
                        <x-ui.icon name="magnifying-glass" class="h-4 w-4" />
                        Cari
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </section>

    {{-- Buku terbaru --}}
    <section class="bg-background px-4 pb-16 pt-14 sm:px-6 sm:pb-20 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <x-home.section-heading eyebrow="Katalog Buku Terbaru" icon="sparkles"
                title="Buku terbaru yang baru masuk ke perpustakaan."
                description="Kalau ingin melihat koleksi yang baru ditambahkan, mulai dari sini." />

            <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-5">
                @forelse ($latestBooks as $book)
                    <x-books.book-card :book="$book" meta-label="Ketersediaan" :meta-value="$book->available_copies . ' dari ' . $book->total_copies . ' buku'" />
                @empty
                    <div class="sm:col-span-2 xl:col-span-3">
                        <x-ui.card>
                            <p class="text-sm leading-6 text-muted-foreground">
                                Belum ada data buku terbaru untuk ditampilkan.
                            </p>
                        </x-ui.card>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Buku sering dipinjam --}}
    <section class="border-y border-border bg-card px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <x-home.section-heading eyebrow="Sering Dipinjam" icon="fire" title="Buku yang paling sering dipinjam."
                description="Bagian ini bisa membantu kalau Anda ingin melihat buku yang banyak diminati." />

            <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-5">
                @forelse ($mostBorrowedBooks as $book)
                    <x-books.book-card :book="$book" meta-label="Total pinjam" :meta-value="((int) ($book->loan_items_sum_quantity ?? 0)) > 0
                        ? ((int) $book->loan_items_sum_quantity) . ' kali'
                        : 'Belum ada histori pinjam'" />
                @empty
                    <div class="sm:col-span-2 xl:col-span-3">
                        <x-ui.card>
                            <p class="text-sm leading-6 text-muted-foreground">
                                Belum ada data buku yang sering dipinjam untuk ditampilkan.
                            </p>
                        </x-ui.card>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

</x-layouts.guest>
