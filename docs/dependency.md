# Dokumentasi Dependency

Versi pada tabel diambil dari lock file dan instalasi proyek per 27 Juni 2026.

## Dependency Backend

| Package | Fungsi | Alasan digunakan | Versi | Risiko/Perhatian |
| --- | --- | --- | --- | --- |
| `laravel/framework` | Framework aplikasi | Routing, Eloquent, validasi, auth, queue, dan fondasi aplikasi | 13.15.0 | Upgrade mayor dapat mengubah API framework |
| `filament/filament` | Panel admin/user | Mempercepat pembuatan CRUD, dashboard, dan halaman profil kustom | 5.6.7 | Resource dan schema perlu mengikuti API Filament 5 |
| `livewire/livewire` | UI reaktif | Katalog publik dan komponen Filament berjalan tanpa frontend SPA terpisah | 4.3.1 | Constraint `*` terlalu longgar dan sebaiknya dikunci |
| `laravel/tinker` | REPL Laravel | Debug dan inspeksi aplikasi pada lingkungan lokal | 3.0.2 | Jangan dipakai untuk mutasi data production tanpa kontrol |
| `barryvdh/laravel-dompdf` | Pembuatan PDF | Disiapkan untuk kebutuhan laporan PDF | 3.1.2 | Konsumsi memori meningkat untuk dokumen besar |
| `spatie/laravel-permission` | Role dan permission | Disiapkan untuk otorisasi granular | 7.4.1 | Saat ini role utama masih disimpan langsung pada kolom `users.role` |

## Dependency Development

| Package | Fungsi | Versi | Risiko/Perhatian |
| --- | --- | --- | --- |
| `pestphp/pest` | Framework test | 4.7.3 | Test database memerlukan `pdo_sqlite` |
| `pestphp/pest-plugin-laravel` | Integrasi Pest dan Laravel | 4.1.0 | Harus kompatibel dengan versi Pest |
| `laravel/pint` | Formatter PHP | 1.29.1 | Jalankan sebelum commit perubahan PHP |
| `laravel/boost` | Tool pengembangan Laravel | 2.4.10 | Hanya diperlukan di development |
| `laravel/pail` | Pembaca log Laravel | 1.2.7 | Jangan mengekspos log sensitif |
| `laravel/pao` | Output test untuk agent | 1.0.6 | Pada sebagian lingkungan Windows perlu `PAO_DISABLE=1` |
| `fakerphp/faker` | Data factory/seeder | 1.24.1 | Data hanya sintetis dan bukan data nyata |
| `mockery/mockery` | Mock object | 1.6.12 | Mock berlebihan dapat membuat test rapuh |
| `nunomaduro/collision` | Tampilan error CLI | 8.9.4 | Dependency development saja |

## Dependency Frontend

| Package | Fungsi | Constraint | Risiko/Perhatian |
| --- | --- | --- | --- |
| `vite` | Build tool frontend | `^8.0.0` | Memerlukan Node.js yang kompatibel |
| `laravel-vite-plugin` | Integrasi Laravel-Vite | `^3.1` | Build gagal jika entry point tidak sesuai |
| `tailwindcss` | Utility-first CSS | `^4.3.0` | Perubahan mayor berbeda dari konfigurasi Tailwind 3 |
| `@tailwindcss/vite` | Plugin Tailwind untuk Vite | `^4.0.0` | Harus selaras dengan Tailwind |
| `@tailwindcss/forms` | Normalisasi komponen form | `^0.5.11` | Style dapat memengaruhi komponen pihak ketiga |
| `@tailwindcss/typography` | Style konten tipografi | `^0.5.19` | Tambahan CSS jika digunakan luas |
| `alpinejs` | Interaksi frontend ringan | `^3.15.12` | State frontend harus tetap sederhana |
| `concurrently` | Menjalankan beberapa proses dev | `^10.0.3` | Digunakan lokal, bukan production |
| `nprogress` | Progress bar navigasi | `^0.2.0` | Interaksi UI dapat terasa mengganggu jika dipicu terlalu sering |

## Cara Instalasi

Instal seluruh dependency sesuai lock file:

```bash
composer install
npm ci
```

Menambahkan dependency PHP:

```bash
composer require vendor/package
```

Menambahkan dependency frontend:

```bash
npm install package-name
```

Setelah mengubah dependency:

```bash
composer validate
npm run build
php artisan test --compact
```

## Dampak Dependency terhadap Proyek

- Filament mengurangi jumlah kode CRUD tetapi meningkatkan keterikatan pada
  API panel builder.
- Livewire membuat katalog reaktif sekaligus menjadi fondasi komponen Filament.
- Tailwind dan Vite mempercepat pengembangan UI, tetapi membutuhkan proses
  build Node.js.
- DomPDF dan Spatie Permission telah dideklarasikan untuk kebutuhan laporan
  dan otorisasi lanjutan, tetapi penggunaannya perlu diaudit agar dependency
  yang tidak terpakai tidak menambah beban maintenance.

## Strategi Pemeliharaan

1. Gunakan `composer outdated --direct` dan `npm outdated` secara berkala.
2. Hindari constraint tanpa batas seperti `livewire/livewire: "*"`.
3. Commit `composer.lock` dan `package-lock.json`.
4. Jalankan CI setiap ada perubahan dependency.
5. Uji upgrade mayor pada branch terpisah.
