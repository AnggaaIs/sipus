<x-layouts.auth title="Masuk - SIPUS">
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

                        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
                            @csrf

                            <x-ui.text-field name="login" label="Email atau NIS"
                                placeholder="Masukkan NIS atau email Anda" autocomplete="username" required autofocus />

                            <x-ui.text-field name="password" label="Kata sandi" type="password"
                                placeholder="Masukkan kata sandi" autocomplete="current-password" required />

                            <div class="flex items-center justify-between gap-4 text-sm">
                                <x-ui.checkbox name="remember" label="Ingat saya" />

                                <a href="{{ route('password.request') }}"
                                    class="font-medium text-primary transition-colors duration-200 hover:text-foreground">
                                    Lupa kata sandi?
                                </a>
                            </div>

                            <x-ui.button type="submit" variant="primary" size="lg" full-width>
                                Masuk
                            </x-ui.button>
                        </form>

                        <div class="mt-6 text-sm">
                            <p class="text-muted-foreground">
                                Belum punya akun?
                                <a href="{{ route('register') }}"
                                    class="font-medium text-primary transition-colors duration-200 hover:text-foreground">
                                    Daftar
                                </a>
                            </p>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </section>
</x-layouts.auth>