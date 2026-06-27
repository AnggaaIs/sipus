# Instalasi SIPUS

Dokumen ini menjelaskan pemasangan SIPUS untuk lingkungan pengembangan lokal.

## Persyaratan Sistem

| Komponen | Versi minimum/rekomendasi |
| --- | --- |
| PHP | 8.3 atau lebih baru; proyek dikembangkan dengan PHP 8.4 |
| Composer | 2.x |
| Node.js | Versi LTS yang mendukung Vite 8 |
| npm | Mengikuti instalasi Node.js |
| MySQL | 8.x atau kompatibel |
| Git | 2.x |

Ekstensi PHP yang diperlukan antara lain `ctype`, `curl`, `dom`, `fileinfo`,
`intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, dan `zip`.
Untuk menjalankan test dengan konfigurasi bawaan, aktifkan juga `pdo_sqlite`.

## Langkah Instalasi

### 1. Clone repository

```bash
git clone git@github.com:AnggaaIs/sipus.git
cd sipus
```

Alternatif HTTPS:

```bash
git clone https://github.com/AnggaaIs/sipus.git
cd sipus
```

### 2. Install dependency

```bash
composer install
npm ci
```

Gunakan `npm install` apabila `package-lock.json` belum tersedia.

### 3. Setup environment

Pada Linux/macOS:

```bash
cp .env.example .env
```

Pada PowerShell:

```powershell
Copy-Item .env.example .env
```

Kemudian buat application key:

```bash
php artisan key:generate
```

### 4. Setup database

Buat database MySQL, misalnya `sipus`, lalu atur `.env`:

```env
APP_NAME=SIPUS
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipus
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Seeder menyediakan akun pengembangan:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@sipus.com` | `password` |
| User | `budi.santoso@sipus.com` | `password` |

Kredensial ini hanya untuk pengembangan. Jangan digunakan pada production.

### 5. Build aset

```bash
npm run build
```

Untuk pengembangan dengan hot reload:

```bash
npm run dev
```

### 6. Menjalankan aplikasi

Cara paling lengkap:

```bash
composer run dev
```

Atau jalankan server Laravel secara terpisah:

```bash
php artisan serve
```

Endpoint utama:

| Halaman | URL |
| --- | --- |
| Landing page | `http://localhost:8000` |
| Katalog | `http://localhost:8000/katalog` |
| Login | `http://localhost:8000/login` |
| Panel admin | `http://localhost:8000/admin` |
| Panel user | `http://localhost:8000/user` |

Catatan panel user:

- Avatar profil memakai inisial bawaan Filament.
- User biasa hanya dapat mengganti kata sandi dari menu profil.
- Perubahan nama, email, NISN, kelas, dan nomor telepon harus dilakukan admin.

## Menjalankan Test

```bash
php artisan test --compact
```

Konfigurasi test menggunakan SQLite in-memory. Pastikan ekstensi
`pdo_sqlite` aktif pada PHP CLI.

## Troubleshooting

### Perubahan frontend tidak tampil

```bash
npm run build
php artisan optimize:clear
```

Saat mengembangkan, gunakan `npm run dev` dan pastikan Vite tetap berjalan.

### Vite manifest tidak ditemukan

```bash
npm ci
npm run build
```

### Route atau konfigurasi lama masih digunakan

```bash
php artisan optimize:clear
```

### Permission storage pada Linux

```bash
chmod -R 775 storage bootstrap/cache
```

Pastikan web server memiliki kepemilikan atau akses tulis yang sesuai.

### Test gagal dengan `could not find driver`

Aktifkan ekstensi SQLite untuk PHP CLI:

```ini
extension=pdo_sqlite
extension=sqlite3
```

Periksa konfigurasi yang digunakan:

```bash
php --ini
php -m
```

### Dependency di `composer.json` belum ada di folder `vendor`

Hal ini dapat terjadi setelah berpindah branch. Sinkronkan dependency:

```bash
composer install
php artisan optimize:clear
```
