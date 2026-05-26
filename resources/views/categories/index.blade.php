<x-layouts.guest title="Kategori Buku - SIPUS"
    description="Jelajahi koleksi perpustakaan berdasarkan kategori untuk menemukan buku sesuai minat dan kebutuhan Anda.">
    {{-- Header --}}
    <section class="border-b border-(--border) bg-(--card) px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-3">
            <div class="flex items-center gap-2 text-sm font-medium text-(--primary)">
                <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                <p>Kategori Buku</p>
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-(--foreground) sm:text-4xl">
                Jelajahi koleksi berdasarkan kategori.
            </h1>
            <p class="max-w-2xl text-base leading-7 text-(--muted-foreground)">
                Pilih kategori di bawah ini untuk menemukan buku-buku yang sesuai dengan minat Anda.
            </p>
        </div>
    </section>

    {{-- Categories Grid --}}
    <section class="bg-(--background) px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            @if ($categories->count() > 0)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.show', $category) }}"
                            class="group relative overflow-hidden rounded-xl border border-(--border) bg-(--card) p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-(--primary)/40">

                            {{-- Color accent bar --}}
                            <div class="absolute inset-x-0 top-0 h-1 rounded-t-xl"
                                style="background-color: {{ $category->color ?? '#6366f1' }};"></div>

                            <div class="pt-2 space-y-3">
                                {{-- Icon --}}
                                <div class="flex h-11 w-11 items-center justify-center rounded-lg"
                                    style="background-color: {{ $category->color ?? '#6366f1' }}20;">
                                    <x-ui.icon name="tag" class="h-5 w-5"
                                        style="color: {{ $category->color ?? '#6366f1' }};" />
                                </div>

                                {{-- Name & count --}}
                                <div>
                                    <h2 class="font-semibold text-(--foreground) group-hover:text-(--primary) transition-colors duration-200">
                                        {{ $category->name }}
                                    </h2>
                                    <p class="mt-1 text-sm text-(--muted-foreground)">
                                        {{ number_format($category->books_count) }} buku
                                    </p>
                                </div>

                                @if ($category->description)
                                    <p class="text-sm leading-6 text-(--muted-foreground) line-clamp-2">
                                        {{ $category->description }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-1 text-xs font-medium text-(--primary)">
                                    Lihat buku
                                    <x-ui.icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" />
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <x-ui.card>
                    <div class="space-y-3 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-(--muted)">
                            <x-ui.icon name="squares-2x2" class="h-5 w-5 text-(--muted-foreground)" />
                        </div>
                        <h3 class="text-xl font-semibold text-(--foreground)">Belum ada kategori</h3>
                        <p class="text-sm text-(--muted-foreground)">
                            Kategori buku belum tersedia saat ini.
                        </p>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </section>
</x-layouts.guest>
