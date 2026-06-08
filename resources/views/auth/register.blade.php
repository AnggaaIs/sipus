<x-layouts.auth title="Daftar - SIPUS">
    <section class="min-h-screen bg-background">
        <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full items-stretch gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(380px,430px)] lg:gap-8">
                <div class="hidden lg:block">
                    <x-ui.card padding="none" class="flex h-full min-h-[42rem] overflow-hidden">
                        <img src="{{ asset('images/Login.jpg') }}" alt="Logo SMA Semen Padang"
                            class="h-full w-full object-cover">
                    </x-ui.card>
                </div>

                <x-ui.card padding="md" class="mx-auto flex w-full max-w-md">
                    <div class="flex w-full flex-col">
                        <div class="mb-6">
                            <a href="{{ url('/') }}"
                                class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors duration-200 hover:text-foreground">
                                <span aria-hidden="true">&larr;</span>
                                <span>Kembali ke beranda</span>
                            </a>
                        </div>

                        <div class="space-y-3">
                            <h2
                                class="text-2xl font-semibold tracking-tight text-card-foreground sm:text-[1.75rem]">
                                Masuk ke akun Anda
                            </h2>
                            <p class="text-sm leading-6 text-muted-foreground">
                                Gunakan NIS atau email beserta kata sandi yang sudah terdaftar di SIPUS.
                            </p>
                        </div>

                        @if (session('status'))
                            <x-ui.card padding="sm"
                                class="mt-6 border-primary/20 bg-primary/5">
                                <p class="text-sm leading-6 text-foreground">
                                    {{ session('status') }}
                                </p>
                            </x-ui.card>
                        @endif

                        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
                            @csrf

                                <x-ui.text-field name="full_name" label="Nama lengkap" placeholder="Masukkan nama lengkap"
                                required autofocus />

                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-ui.text-field name="nisn" label="NISN" placeholder="10 digit NISN"
                                    inputmode="numeric" required />

                                <x-ui.text-field name="class" label="Kelas" placeholder="Contoh: XI IPA 2"
                                    required />
                            </div>

                            <x-ui.text-field name="email" label="Email" type="email"
                                placeholder="Masukkan email aktif" autocomplete="email" required />

                            <x-ui.text-field name="phone" label="Nomor telepon" type="tel" placeholder="Opsional"
                                inputmode="tel" />

                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-ui.text-field name="password" label="Kata sandi" type="password"
                                    placeholder="Minimal 8 karakter" autocomplete="new-password" required />

                                <x-ui.text-field name="password_confirmation" label="Konfirmasi kata sandi"
                                    type="password" placeholder="Ulangi kata sandi" autocomplete="new-password"
                                    required />
                            </div>
                            <a href="/login">
                                <x-ui.button type="submit" variant="primary" size="lg" full-width>
                                    Daftar
                                </x-ui.button>
                                </a>
                        </form>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </section>
</x-layouts.auth>