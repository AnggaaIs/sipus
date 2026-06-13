# Dokumentasi GitHub Actions

## Workflow yang Digunakan

SIPUS menggunakan workflow Continuous Integration (CI) untuk memverifikasi
dependency, menyiapkan database, membangun aset frontend, dan menjalankan test
secara otomatis.

## Lokasi File

```text
.github/workflows/ci.yml
```

## Trigger

Workflow berjalan saat:

- push ke branch `main`, `angga`, `naya`, `ihza`, atau `alya`;
- pull request menuju branch `main`;
- dijalankan manual melalui `workflow_dispatch`.

Branch `testing` tidak termasuk karena direncanakan untuk dihapus.

## Environment

| Komponen | Konfigurasi |
| --- | --- |
| Runner | Ubuntu terbaru |
| PHP | 8.4 |
| Node.js | 22 |
| Database test | File SQLite sementara pada runner |
| Dependency PHP | Composer berdasarkan `composer.lock` |
| Dependency frontend | npm dengan cache berdasarkan lockfile |

## Tahapan Workflow

1. Checkout source code.
2. Setup PHP beserta ekstensi yang diperlukan.
3. Setup Node.js.
4. Validasi konfigurasi Composer.
5. Install dependency Composer berdasarkan `composer.lock`.
6. Audit kerentanan dependency PHP.
7. Install dependency npm berdasarkan `package-lock.json`.
8. Audit kerentanan dependency frontend severity tinggi atau kritis.
9. Membuat `.env` dan application key.
10. Membuat database SQLite dan menjalankan migration.
11. Build aset dengan Vite.
12. Memastikan config, event, route, view, dan metadata aplikasi dapat
    di-cache untuk production.
13. Menjalankan test Pest.

## Perintah Verifikasi

```bash
composer validate --no-check-publish
composer audit --locked --no-interaction
npm audit --audit-level=high
npm run build
php artisan optimize
vendor/bin/pest --ci
```

## Hasil Workflow

Status dapat dilihat pada tab **Actions** repository:

```text
https://github.com/AnggaaIs/sipus/actions
```

Badge CI ditampilkan pada README:

```markdown
[![CI](https://github.com/AnggaaIs/sipus/actions/workflows/ci.yml/badge.svg)](
https://github.com/AnggaaIs/sipus/actions/workflows/ci.yml)
```

Screenshot hasil workflow dapat ditambahkan setelah workflow pertama selesai:

```text
docs/images/github-actions-success.png
```

## Interpretasi Hasil

- Hijau: seluruh tahap selesai dan perubahan layak ditinjau lebih lanjut.
- Merah: buka job yang gagal dan periksa langkah pertama yang menghasilkan
  error.
- Kuning/queued: runner masih menunggu atau workflow sedang berjalan.

## Batas Jaminan CI

CI ini memastikan bahwa pada environment Ubuntu, PHP 8.4, Node.js 22, dan
SQLite:

- dependency dapat dipasang dari lockfile;
- migration dapat dijalankan;
- aset frontend dapat dibangun;
- cache production Laravel dapat dibuat;
- dependency tidak memiliki advisori yang terdeteksi oleh Composer atau npm
  sesuai tingkat kegagalan workflow;
- seluruh test Pest yang tersedia berhasil.

CI tidak membuktikan seluruh fitur berjalan dan tidak dapat menjamin aplikasi
sepenuhnya aman. Saat ini test yang tersedia hanya dua test contoh, sehingga
alur login, pemisahan role, policy, CRUD, upload, dan integrasi antarmuka belum
tercakup. Audit dependency juga tidak menemukan kelemahan logika aplikasi,
konfigurasi server, kebocoran secret, SQL injection khusus implementasi, XSS,
CSRF yang salah diterapkan, maupun masalah otorisasi tanpa test atau alat
analisis tambahan.

Hasil hijau berarti pemeriksaan yang tercantum di atas lulus, bukan sertifikasi
keamanan.

## Status Audit Saat Ini

Pada 13 Juni 2026, audit lokal masih menemukan advisori pada dependency PHP
dan dua kerentanan kritis npm pada `shell-quote` melalui `concurrently`.
Workflow akan gagal pada tahap audit sampai lockfile diperbarui ke versi yang
sudah diperbaiki.

## Pengembangan Berikutnya

- Tambahkan test feature untuk autentikasi dan redirect dua role.
- Tambahkan test policy setiap resource.
- Tambahkan test CRUD penting.
- Tambahkan browser smoke test ketika UI sudah stabil.
- Tambahkan static analysis dan secret scanning.
- Unggah artifact log atau screenshot jika diperlukan untuk laporan PBL.
