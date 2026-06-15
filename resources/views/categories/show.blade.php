<x-layouts.guest :title="$category->name . ' - Kategori Buku - SIPUS'" :description="$category->description ?: 'Lihat daftar buku pada kategori ' . $category->name . ' di perpustakaan SIPUS.'">
    {{-- Header --}}
    <section class="border-b border-(--border) bg-(--card) px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-4">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-(--muted-foreground)">
                <a href="{{ route('categories.index') }}" class="hover:text-(--foreground) transition-colors">Kategori</a>
                <x-ui.icon name="chevron-right" class="h-3.5 w-3.5" />
                <span class="text-(--foreground) font-medium">{{ $category->name }}</span>
            </nav>

            <div class="flex items-center gap-4">
                {{-- Titik warna kategori --}}
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                    style="background-color: {{ $category->color ?? '#6366f1' }}20;">
                    <x-ui.icon name="tag" class="h-6 w-6" style="color: {{ $category->color ?? '#6366f1' }};" />
                </div>
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-(--foreground) sm:text-4xl">
                        {{ $category->name }}
                    </h1>
                    @if ($category->description)
                        <p class="mt-1 text-base leading-7 text-(--muted-foreground)">
                            {{ $category->description }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Daftar buku --}}
    <section class="bg-(--background) px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-1">
                    <h2 class="text-2xl font-semibold tracking-tight text-(--foreground)">
                        Koleksi buku kategori ini
                    </h2>
                    <p class="text-sm leading-6 text-(--muted-foreground)">
                        @if ($books->total() > 0)
                            Menampilkan {{ $books->firstItem() }}&ndash;{{ $books->lastItem() }} dari
                            {{ $books->total() }} buku.
                        @else
                            Belum ada buku dalam kategori ini.
                        @endif
                    </p>
                </div>
                <x-ui.button :href="route('categories.index')" variant="ghost" size="md">
                    <x-ui.icon name="arrow-left" class="h-4 w-4" />
                    Semua kategori
                </x-ui.button>
            </div>

            @if ($books->count() > 0)
                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-5">
                    @foreach ($books as $book)
                        <x-books.book-card :book="$book" meta-label="Ketersediaan" :meta-value="$book->available_copies . ' dari ' . $book->total_copies . ' buku'" />
                    @endforeach
                </div>

                @if ($books->hasPages())
                    <div class="pt-2">
                        {{ $books->onEachSide(1)->links() }}
                    </div>
                @endif
            @else
                <x-ui.card>
                    <div class="space-y-3 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-(--muted)">
                            <x-ui.icon name="book-open" class="h-5 w-5 text-(--muted-foreground)" />
                        </div>
                        <h3 class="text-xl font-semibold text-(--foreground)">Belum ada buku</h3>
                        <p class="text-sm leading-6 text-(--muted-foreground)">
                            Kategori ini belum memiliki buku yang tersedia.
                        </p>
                        <div class="pt-1">
                            <x-ui.button :href="route('categories.index')" variant="ghost" size="lg">
                                <x-ui.icon name="squares-2x2" class="h-4 w-4" />
                                Lihat kategori lain
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </section>
</x-layouts.guest>
