<nav class="sticky top-0 z-50 border-b border-gray-200 bg-white/80 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        
        <!-- Logo -->
        <a href="#" class="flex items-center gap-3">
            <img src="{{ ('images/sepang_sma_logo.png') }}" alt="Logo" class="h-10 w-10 rounded-lg">
            <div>
                <h1 class="text-lg font-bold text-slate-900">SIPUS</h1>
                <p class="text-xs text-slate-500">Sistem Informasi Perpustakaan</p>
            </div>
        </a>

        <!-- Right Side -->
        <div class="hidden items-center gap-3 md:flex">

                <div class="hidden items-center gap-8 md:flex">
            <a href="#"
                class="font-medium text-slate-600 transition hover:text-blue-600">
                Home
            </a>

            <a href="#"
                class="font-medium text-slate-600 transition hover:text-blue-600">
                Katalog Buku
            </a>
            <a href="login" class="hover:text-blue-600">
                <x-ui.button class="px-6 py-3">
                Login
                </x-ui.button>
            </a>
        </div>

        </div>

        <!-- Mobile Button -->
        <button id="mobile-menu-btn" class="md:hidden">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-slate-700"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden border-t border-gray-200 bg-white md:hidden">
        <div class="space-y-2 p-4">

            <a href="#"
                class="block rounded-lg px-3 py-2 hover:bg-gray-100">
                Beranda
            </a>

            <a href="#"
                class="block rounded-lg px-3 py-2 hover:bg-gray-100">
                Katalog Buku
            </a>

            <a href="#"
                class="block rounded-lg px-3 py-2 hover:bg-gray-100">
                Kategori
            </a>

            @guest
                <a href="#"
                    class="block rounded-lg px-3 py-2 hover:bg-gray-100">
                    Masuk
                </a>

                <a href="#"
                    class="block rounded-lg bg-blue-600 px-3 py-2 text-white">
                    Daftar
                </a>
            @endguest
        </div>
    </div>
</nav>

<script>
document.getElementById('mobile-menu-btn').addEventListener('click', () => {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});
</script>