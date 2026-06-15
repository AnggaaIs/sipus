<x-layouts.guest title="404 - Halaman Tidak Ditemukan">
    <section class="bg-[var(--background)]">
        <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
            <div class="space-y-4 py-12 text-center sm:py-16">
                <p class="text-7xl font-semibold tracking-tight text-[var(--primary)] sm:text-8xl">404</p>
                <h1 class="text-3xl font-semibold tracking-tight text-[var(--foreground)] sm:text-4xl">
                    Halaman tidak ditemukan
                </h1>
                <x-ui.button :href="route('home')" variant="primary" size="md">
                    Kembali ke beranda
                </x-ui.button>
            </div>
        </div>
    </section>
</x-layouts.guest>
