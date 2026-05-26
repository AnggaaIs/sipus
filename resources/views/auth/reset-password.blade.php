<x-layouts.auth title="Atur Ulang Kata Sandi - SIPUS">
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
                    <p class="text-sm font-medium text-primary">Atur ulang kata sandi</p>
                    <h1 class="text-2xl font-semibold tracking-tight text-card-foreground">
                        Buat kata sandi baru
                    </h1>
                    <p class="text-sm leading-6 text-muted-foreground">
                        Gunakan kata sandi baru untuk masuk kembali ke akun SIPUS Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <x-ui.text-field name="email" label="Email" type="email" :value="request('email')"
                        placeholder="Masukkan email akun Anda" autocomplete="email" required autofocus />

                    <x-ui.text-field name="password" label="Kata sandi baru" type="password"
                        placeholder="Masukkan kata sandi baru" autocomplete="new-password" required />

                    <x-ui.text-field name="password_confirmation" label="Konfirmasi kata sandi" type="password"
                        placeholder="Ulangi kata sandi baru" autocomplete="new-password" required />

                    <x-ui.button type="submit" variant="primary" size="lg" full-width>
                        Simpan kata sandi baru
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </section>
</x-layouts.auth>
