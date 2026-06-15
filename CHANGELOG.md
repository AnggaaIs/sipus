# Changelog

Semua perubahan penting pada SIPUS dicatat di dokumen ini. Format mengikuti
prinsip [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) dan versi
menggunakan [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Dokumentasi instalasi, fitur, dependency, refactoring, dan GitHub Actions.
- Workflow CI untuk build aset dan menjalankan Pest.
- Screenshot panel admin dan fitur CRUD utama.
- Halaman daftar kategori buku untuk akses pengunjung.
- CRUD Penulis dan Penerbit di panel admin.
- Manajemen pengembalian buku di panel admin.

### Changed

- README diperbarui agar menjadi pintu masuk ke dokumentasi proyek.

## [0.3.0] - 2026-06-13

### Added

- Panel user Filament pada path `/user`.
- CRUD DDC, pengguna, dan peminjaman pada panel admin.
- Delapan model policy untuk membatasi akses berdasarkan role dan status akun.
- Middleware redirect autentikasi menuju halaman login publik.
- Mode SPA dan pengelompokan navigasi resource Filament.

### Changed

- Model, factory, seeder, view, dan route diselaraskan dengan struktur database.
- Model pengguna menggunakan role `admin` dan `user` serta status akun
  `pending`, `active`, `rejected`, atau `suspended`.
- Resource peminjaman lama dihapus dan diganti dengan resource berbasis model
  `Loan`.

### Removed

- CRUD `Borrow` dan CRUD pengembalian lama yang tidak lagi sesuai dengan model
  transaksi terbaru.

## [0.2.0] - 2026-06-09

### Added

- README awal berisi deskripsi, fitur, teknologi, instalasi, dan tim.
- Landing page, katalog buku, serta komponen antarmuka publik.

## [0.1.0] - 2026-06-02

### Added

- Autentikasi login dan registrasi.
- Struktur database perpustakaan, model Eloquent, factory, dan seeder.
- Panel admin Filament dan resource CRUD awal.

[Unreleased]: https://github.com/AnggaaIs/sipus/compare/main...HEAD
[0.3.0]: https://github.com/AnggaaIs/sipus/commits/main/
[0.2.0]: https://github.com/AnggaaIs/sipus/commits/374bf0d
[0.1.0]: https://github.com/AnggaaIs/sipus/commits/a3ae17b
