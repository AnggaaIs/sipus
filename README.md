<p align="center">
  <img src="public/images/sepang_sma_logo.png" alt="Logo SMA Semen Padang" width="120">
</p>

<h1 align="center">SIPUS</h1>

<p align="center">
  <strong>Sistem Informasi Perpustakaan SMA Semen Padang</strong>
</p>

## Deskripsi Proyek

SIPUS adalah aplikasi berbasis web yang membantu digitalisasi layanan
perpustakaan SMA Semen Padang. Aplikasi ini menyediakan katalog buku yang
dapat diakses oleh pengguna serta panel administrasi untuk mengelola koleksi,
kategori, peminjaman, pengembalian, dan denda secara terpusat.

SIPUS dikembangkan untuk mempermudah pencarian buku, meningkatkan efisiensi
pengelolaan data perpustakaan, dan mendukung budaya literasi di lingkungan
sekolah.

## Fitur Utama

- Beranda yang menampilkan koleksi buku terbaru.
- Katalog buku dengan pencarian berdasarkan judul, penulis, ISBN, dan DDC.
- Filter koleksi berdasarkan klasifikasi DDC.
- Informasi ketersediaan dan jumlah eksemplar buku.
- Registrasi dan autentikasi pengguna.
- Panel administrasi berbasis Filament.
- Pengelolaan data buku dan kategori.
- Pengelolaan transaksi peminjaman dan pengembalian buku.
- Pengelolaan denda keterlambatan.
- Manajemen data pengguna dengan peran admin dan siswa.
- Antarmuka responsif untuk perangkat desktop dan mobile.

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
| --- | --- |
| PHP 8.3+ | Bahasa pemrograman backend |
| Laravel 13 | Framework utama aplikasi |
| Filament 5 | Panel administrasi |
| Livewire 4 | Komponen antarmuka reaktif |
| Alpine.js 3 | Interaksi ringan pada sisi pengguna |
| Tailwind CSS 4 | Styling antarmuka |
| Vite 8 | Build tool aset frontend |
| MySQL | Basis data aplikasi |
| Pest 4 | Pengujian aplikasi |

## Instalasi Singkat

### Prasyarat

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- MySQL

### Langkah Instalasi

1. Clone repositori dan masuk ke direktori proyek.

   ```bash
   git clone <url-repositori>
   cd sipus
   ```

2. Instal dependensi backend dan frontend.

   ```bash
   composer install
   npm install
   ```

3. Salin berkas konfigurasi lingkungan dan buat application key.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Buat database MySQL bernama `sipus`, kemudian sesuaikan konfigurasi
   database pada berkas `.env`.

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sipus
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Jalankan migrasi dan seeder.

   ```bash
   php artisan migrate --seed
   ```

6. Build aset frontend.

   ```bash
   npm run build
   ```

7. Jalankan aplikasi.

   ```bash
   composer run dev
   ```

Aplikasi dapat dibuka melalui `http://localhost:8000`, sedangkan panel admin
tersedia di `http://localhost:8000/admin`.

### Akun Admin Default

| Email | Password |
| --- | --- |
| `admin@sipus.com` | `password` |

> Akun di atas berasal dari seeder dan hanya ditujukan untuk lingkungan
> pengembangan. Ganti kredensial sebelum aplikasi digunakan pada lingkungan
> produksi.

## Screenshot Proyek

### Halaman Utama

![Perpustakaan SMA Semen Padang](public/images/Perpustakaan.jpg)

### Halaman Autentikasi

![SMA Semen Padang](public/images/Login.jpg)

## Tim Pengembang

| Nama | NIM |
| --- | --- |
| Angga Islami Pasya | 2411081004 |
| Ihza Mahendra | 2411082009 |
| Inayah Henni El Najla | 2411081010 |
| Alya Dhiya Najla | 2411081003 |

---

<p align="center">
  Dikembangkan untuk mendukung pengelolaan perpustakaan dan budaya literasi
  di SMA Semen Padang.
</p>
