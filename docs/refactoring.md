# Dokumentasi Refactoring

Dokumen ini mencatat perubahan struktur yang meningkatkan konsistensi dan
kemudahan pemeliharaan tanpa mengubah tujuan utama aplikasi.

## Standardisasi Model, Factory, dan Seeder

**Sebelum**

- Nama kolom pengguna tidak konsisten antara `nis`, `nisn`, `is_approved`,
  dan `account_status`.
- Role menggunakan campuran nilai `siswa` dan `user`.
- Factory dan seeder dapat menulis kolom yang tidak tersedia pada database.

**Masalah**

Seeder gagal dan logika autentikasi tidak memiliki satu kontrak data.

**Perubahan**

- Kolom identitas distandardisasi menjadi `nisn`.
- Role distandardisasi menjadi `admin` dan `user`.
- Status persetujuan menggunakan `account_status`.
- Model, factory, seeder, route, dan view diselaraskan.

**Alasan**

Satu vocabulary domain mengurangi bug schema dan mempermudah validasi.

**Dampak**

Seeder, autentikasi, dan akses panel memakai kontrak akun yang sama.

**Bukti commit:** `eee22d6`, `2d1a6cc`.

## Pemisahan Authorization ke Model Policy

**Sebelum**

Pembatasan aksi resource bergantung pada akses panel dan berpotensi tersebar
di komponen UI.

**Masalah**

Menyembunyikan tombol tidak cukup untuk melindungi operasi backend.

**Perubahan**

Ditambahkan policy untuk Author, Book, Category, DDC, Fine, Loan, Publisher,
dan User.

**Alasan**

Authorization harus berada pada lapisan domain Laravel dan dapat digunakan
kembali oleh Filament maupun controller.

**Dampak**

Admin aktif mendapatkan aksi yang sesuai, sementara pengguna atau akun yang
belum disetujui ditolak. Admin juga tidak dapat menghapus akunnya sendiri
melalui `UserPolicy`.

**Bukti commit:** `bb03cbb`.

## Penggantian Konsep Borrow ke Loan

**Sebelum**

Aplikasi memiliki resource dan controller `Borrow` yang terpisah dari model
transaksi `Loan`.

**Masalah**

Dua istilah untuk proses yang sama menimbulkan duplikasi dan kebingungan
relasi.

**Perubahan**

CRUD `Borrow` dihapus. Transaksi dipusatkan pada `Loan` dan `LoanItem`.

**Alasan**

Struktur transaksi menjadi lebih jelas: satu header peminjaman dapat memiliki
banyak item buku.

**Dampak**

Resource peminjaman lebih konsisten dengan schema database dan relasi
Eloquent.

**Bukti commit:** `6b1eca2`, `d339b10`.

## Pemisahan Panel Admin dan User

**Sebelum**

Semua kebutuhan Filament diarahkan ke panel admin.

**Masalah**

Admin dan pengguna memiliki kebutuhan navigasi serta hak akses yang berbeda.

**Perubahan**

- `AdminPanelProvider` melayani `/admin`.
- `UserPanelProvider` melayani `/user`.
- `User::canAccessPanel()` membatasi panel berdasarkan role dan status akun.
- Controller login mengarahkan role ke dashboard yang sesuai.

**Alasan**

Pemisahan panel mengurangi risiko resource admin terlihat oleh pengguna.

**Dampak**

Struktur siap dikembangkan dengan resource khusus pengguna tanpa mencampur
CRUD operasional admin.

**Bukti commit:** `78194fe`.

## Penggunaan Form dan Table Class pada Resource Filament

**Sebelum**

Schema form dan konfigurasi tabel berpotensi menumpuk di kelas resource.

**Masalah**

Resource menjadi panjang dan sulit dipindai.

**Perubahan**

Setiap resource memisahkan:

- `*Resource.php` untuk metadata dan routing;
- `Schemas/*Form.php` untuk field form;
- `Tables/*Table.php` untuk kolom, filter, dan action;
- `Pages/` untuk halaman list, create, dan edit.

**Alasan**

Setiap kelas memiliki satu tanggung jawab yang lebih jelas.

**Dampak**

CRUD lebih mudah dikembangkan dan diuji secara terpisah.

## Pengelompokan Navigasi dan SPA Filament

**Sebelum**

Resource tampil tanpa pengelompokan domain yang konsisten.

**Masalah**

Navigasi panel sulit dipindai ketika jumlah CRUD bertambah.

**Perubahan**

- Resource koleksi dikelompokkan pada `Manajemen Perpustakaan`.
- Transaksi dikelompokkan pada `Sirkulasi`.
- Mode SPA Filament diaktifkan pada panel admin.

**Alasan**

Navigasi perlu mengikuti aktivitas kerja admin.

**Dampak**

Menu lebih terstruktur dan perpindahan halaman panel terasa lebih cepat.

**Bukti commit:** `0515a5b`.

## Rencana Refactoring Berikutnya

- Menghapus method CRUD kosong pada `BookController` atau menggantinya dengan
  controller single-action yang hanya menangani detail buku.
- Memberi return type pada seluruh method controller dan Livewire.
- Menambahkan service untuk operasi transaksi peminjaman yang kompleks.
- Menambah test authorization, autentikasi dua panel, dan CRUD inti.
- Meninjau dependency DomPDF dan Spatie Permission apabila belum digunakan.
