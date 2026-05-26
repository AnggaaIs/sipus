# Identifikasi Dependency/Package Laravel PBL SIPUS

## 1. `laravel/framework`

| 5W+1H | Jawaban |
| --- | --- |
| What | Framework utama Laravel |
| Why | Menjalankan fitur inti aplikasi |
| Who | Developer dan seluruh pengguna sistem |
| When | Sejak awal aplikasi dijalankan |
| Where | Seluruh modul aplikasi |
| How | Diinstal via Composer dan dipakai sebagai fondasi project |

Referensi: https://laravel.com/docs/13.x

## 2. `filament/filament`

| 5W+1H | Jawaban |
| --- | --- |
| What | Package admin panel Laravel |
| Why | Mempermudah pembuatan dashboard dan CRUD |
| Who | Developer, admin, user panel |
| When | Saat mengelola data buku, user, kategori, dan peminjaman |
| Where | Panel admin dan panel user |
| How | Diinstal via Composer lalu dikonfigurasi lewat resource, page, dan widget |

Referensi: https://filamentphp.com/docs/5.x/introduction/overview/

## 3. `livewire/livewire`

| 5W+1H | Jawaban |
| --- | --- |
| What | Package UI interaktif Laravel |
| Why | Membuat halaman lebih dinamis tanpa banyak JavaScript manual |
| Who | Developer dan pengguna aplikasi |
| When | Saat fitur butuh pencarian, filter, atau update data interaktif |
| Where | Katalog buku dan komponen panel |
| How | Dibuat dalam bentuk komponen Livewire dan dipanggil di route atau Blade |

Referensi: https://livewire.laravel.com/docs/4.x/components

## 4. `blade-ui-kit/blade-icons`

| 5W+1H | Jawaban |
| --- | --- |
| What | Package ikon untuk Blade |
| Why | Mempermudah penggunaan ikon pada tampilan |
| Who | Developer dan pengguna aplikasi |
| When | Saat membuat navbar, tombol, atau elemen visual |
| Where | View Blade dan komponen UI |
| How | Ikon dipanggil melalui komponen Blade |

Referensi: https://blade-ui-kit.com/blade-icons

## 5. `laravel/tinker`

| 5W+1H | Jawaban |
| --- | --- |
| What | Tool CLI untuk menjalankan kode Laravel langsung |
| Why | Membantu debugging dan pengecekan data |
| Who | Developer |
| When | Saat development dan testing manual |
| Where | Terminal project |
| How | Dijalankan dengan perintah `php artisan tinker` |

Referensi: https://laravel.com/docs/13.x/artisan#tinker-repl

## 6. `spatie/laravel-permission`

| 5W+1H | Jawaban |
| --- | --- |
| What | Package role dan permission |
| Why | Mengatur hak akses pengguna lebih detail |
| Who | Developer, admin, petugas |
| When | Saat sistem butuh pembagian akses lebih rinci |
| Where | Modul autentikasi, otorisasi, dan panel admin |
| How | Diinstal via Composer lalu diterapkan pada model user dan permission |

Referensi: https://spatie.be/docs/laravel-permission/v7/introduction

## 7. `maatwebsite/excel`

| 5W+1H | Jawaban |
| --- | --- |
| What | Package export-import Excel/CSV |
| Why | Memudahkan laporan dan rekap data |
| Who | Admin, operator, developer |
| When | Saat export atau import data |
| Where | Modul laporan dan data master |
| How | Diinstal via Composer lalu dipanggil dari controller atau action |

Referensi: https://docs.laravel-excel.com/3.1/getting-started/

## 8. `barryvdh/laravel-dompdf`

| 5W+1H | Jawaban |
| --- | --- |
| What | Package generator PDF dari Blade/HTML |
| Why | Membuat laporan dalam format PDF |
| Who | Admin, operator, developer |
| When | Saat cetak atau unduh laporan |
| Where | Modul laporan |
| How | Diinstal via Composer lalu view dirender menjadi PDF |

Referensi: https://github.com/barryvdh/laravel-dompdf
