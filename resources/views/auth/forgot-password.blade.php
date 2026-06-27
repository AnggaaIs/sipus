<x-layouts.auth title="Lupa Kata Sandi - SIPUS">
    <section class="min-h-screen bg-background">
        <div class="mx-auto flex min-h-screen max-w-md items-center px-4 py-8 sm:px-6 lg:px-8">
            <x-ui.card padding="md" class="w-full">
                <div class="mb-6">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors duration-200 hover:text-foreground">
                        <span aria-hidden="true">&larr;</span>
                        <span>Kembali ke halaman masuk</span>
                    </a>
                </div>

                <div class="space-y-2">
                    <p class="text-sm font-medium text-primary">Lupa kata sandi</p>
                    <h1 class="text-2xl font-semibold tracking-tight text-card-foreground">
                        Kirim tautan atur ulang kata sandi
                    </h1>
                    <p class="text-sm leading-6 text-muted-foreground">
                        Masukkan email akun SIPUS Anda. Kami akan mengirim tautan untuk membuat kata sandi baru.
                    </p>
                </div>

                @if (session('status'))
                    <x-ui.card padding="sm" class="mt-6 border-primary/20 bg-primary/5">
                        <p class="text-sm leading-6 text-foreground">
                            {{ session('status') }}
                        </p>
                    </x-ui.card>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                    @csrf

                    <x-ui.text-field name="email" label="Email" type="email" placeholder="Masukkan email akun Anda"
                        autocomplete="email" required autofocus />

                    <x-ui.button type="submit" variant="primary" size="lg" full-width>
                        Kirim tautan
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </section>
</x-layouts.auth>
