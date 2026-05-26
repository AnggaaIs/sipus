@php
    $coverUrl = $book->cover_url;
    $fallbackLogo = asset('images/sepang_sma_logo.png');
@endphp

<a href="{{ route('books.show', $book) }}"
    class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-[0_8px_30px_-8px_color-mix(in_oklab,var(--color-foreground)_18%,transparent)]">
    {{-- Area sampul --}}
    <div class="relative aspect-[2/3] w-full overflow-hidden">
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="Sampul {{ $book->title }}"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
        @else
            <div
                class="flex h-full w-full flex-col items-center justify-center gap-2 bg-card text-muted-foreground transition-transform duration-500 group-hover:scale-105">
                <img src="{{ $fallbackLogo }}" alt="Logo SMA Semen Padang" class="h-16 w-16 opacity-70" />
                <span class="text-[11px] font-semibold uppercase tracking-[0.12em]">No cover</span>
            </div>
        @endif

        {{-- Penanda ketersediaan --}}
        @if (isset($book->available_copies))
            <span
                class="absolute right-2 top-2 flex items-center gap-1 rounded-full
             bg-black/40 px-1.5 py-0.5 backdrop-blur-sm">
                <span
                    class="h-1.5 w-1.5 rounded-full
                 {{ $book->available_copies > 0 ? 'bg-emerald-400' : 'bg-rose-400' }}">
                </span>
                <span class="text-[10px] font-medium text-white">
                    {{ $book->available_copies > 0 ? 'Tersedia' : 'Tidak Tersedia' }}
                </span>
            </span>
        @endif
    </div>

    {{-- Isi kartu --}}
    <div class="flex flex-1 flex-col gap-2 p-3">
        <div class="flex-1 space-y-1">
            <h3
                class="line-clamp-2 text-sm font-semibold leading-snug text-foreground transition-colors group-hover:text-primary">
                {{ $book->title }}
            </h3>
            @if (!empty($book->author))
                <p class="line-clamp-1 text-xs text-muted-foreground">
                    {{ $book->author }}
                </p>
            @endif
            @if ($book->relationLoaded('ddc') && $book->ddc)
                <p class="line-clamp-1 text-[11px] text-muted-foreground">
                    DDC {{ $book->ddc->code }} - {{ $book->ddc->name }}
                </p>
            @endif
        </div>

        {{-- Informasi tambahan --}}
        @if (isset($metaLabel) && isset($metaValue))
            <div class="border-t border-border pt-2">
                <p class="text-[10px] uppercase tracking-wide text-muted-foreground">{{ $metaLabel }}</p>
                <p class="text-xs font-medium text-foreground">{{ $metaValue }}</p>
            </div>
        @endif
    </div>
</a>
