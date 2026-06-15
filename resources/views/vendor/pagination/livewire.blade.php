@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="space-y-4">
        <div class="flex justify-center sm:hidden">
            <div
                class="inline-flex items-center gap-1 rounded-[calc(var(--radius)+0.125rem)] border border-[var(--border)] bg-[var(--card)] p-1">
                @if ($paginator->onFirstPage())
                    <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)]">
                        <x-ui.icon name="chevron-left" class="h-4 w-4" />
                    </span>
                @else
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        rel="prev"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)] transition-colors duration-200 hover:bg-[var(--accent)] hover:text-[var(--foreground)]"
                        aria-label="Halaman sebelumnya">
                        <x-ui.icon name="chevron-left" class="h-4 w-4" />
                    </button>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span
                            class="inline-flex min-w-10 items-center justify-center px-2 text-sm font-medium text-[var(--muted-foreground)]">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                    class="inline-flex min-w-10 items-center justify-center rounded-[var(--radius)] bg-[var(--primary)] px-3 py-2 text-sm font-semibold text-[var(--primary-foreground)]">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    class="inline-flex min-w-10 items-center justify-center rounded-[var(--radius)] px-3 py-2 text-sm font-medium text-[var(--muted-foreground)] transition-colors duration-200 hover:bg-[var(--accent)] hover:text-[var(--foreground)]"
                                    aria-label="Buka halaman {{ $page }}">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        rel="next"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)] transition-colors duration-200 hover:bg-[var(--accent)] hover:text-[var(--foreground)]"
                        aria-label="Halaman berikutnya">
                        <x-ui.icon name="chevron-right" class="h-4 w-4" />
                    </button>
                @else
                    <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)]">
                        <x-ui.icon name="chevron-right" class="h-4 w-4" />
                    </span>
                @endif
            </div>
        </div>

        <div class="hidden items-center justify-between gap-6 sm:flex">
            <p class="text-sm text-[var(--muted-foreground)]">
                {{ __('Menampilkan') }}
                @if ($paginator->firstItem())
                    <span class="font-semibold text-[var(--foreground)]">{{ $paginator->firstItem() }}</span>
                    {{ __('sampai') }}
                    <span class="font-semibold text-[var(--foreground)]">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {{ __('dari') }}
                <span class="font-semibold text-[var(--foreground)]">{{ $paginator->total() }}</span>
                {{ __('buku') }}
            </p>

            <div
                class="inline-flex items-center gap-1 rounded-[calc(var(--radius)+0.125rem)] border border-[var(--border)] bg-[var(--card)] p-1">
                @if ($paginator->onFirstPage())
                    <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)]">
                        <x-ui.icon name="chevron-left" class="h-4 w-4" />
                    </span>
                @else
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        rel="prev"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)] transition-colors duration-200 hover:bg-[var(--accent)] hover:text-[var(--foreground)]"
                        aria-label="Halaman sebelumnya">
                        <x-ui.icon name="chevron-left" class="h-4 w-4" />
                    </button>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span
                            class="inline-flex min-w-10 items-center justify-center px-2 text-sm font-medium text-[var(--muted-foreground)]">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                    class="inline-flex min-w-10 items-center justify-center rounded-[var(--radius)] bg-[var(--primary)] px-3 py-2 text-sm font-semibold text-[var(--primary-foreground)]">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    class="inline-flex min-w-10 items-center justify-center rounded-[var(--radius)] px-3 py-2 text-sm font-medium text-[var(--muted-foreground)] transition-colors duration-200 hover:bg-[var(--accent)] hover:text-[var(--foreground)]"
                                    aria-label="Buka halaman {{ $page }}">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        rel="next"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)] transition-colors duration-200 hover:bg-[var(--accent)] hover:text-[var(--foreground)]"
                        aria-label="Halaman berikutnya">
                        <x-ui.icon name="chevron-right" class="h-4 w-4" />
                    </button>
                @else
                    <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius)] text-[var(--muted-foreground)]">
                        <x-ui.icon name="chevron-right" class="h-4 w-4" />
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
