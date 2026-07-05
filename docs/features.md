# Dokumentasi Fitur SIPUS

SIPUS melayani dua aktor utama: admin perpustakaan dan pengguna/siswa. Akses
panel dibatasi melalui role, status akun, dan model policy.

## Landing Page

**Tujuan:** Menampilkan identitas perpustakaan, koleksi terbaru, dan koleksi
yang sering dipinjam.

**Aktor:** Pengunjung umum.

**Alur:** Pengunjung membuka beranda, sistem mengambil koleksi terbaru dan
data popularitas peminjaman, lalu menampilkan kartu buku.

**Route dan kode terkait:**

- `GET /`
- `HomeController`
- `resources/views/welcome.blade.php`

![Landing page SIPUS](../public/images/Perpustakaan.jpg)

## Katalog Buku

**Tujuan:** Memudahkan pencarian koleksi sebelum pengguna datang ke
perpustakaan.

**Aktor:** Pengunjung umum.

**Alur:** Pengunjung memasukkan judul, ISBN, penulis, atau DDC; Livewire
memperbarui query dan menampilkan hasil dengan pagination.

**Route dan kode terkait:**

- `GET /katalog`
- `BookCatalog`
- `resources/views/livewire/book-catalog.blade.php`

Filter yang tersedia:

- Kata kunci judul.
- ISBN.
- Nama penulis.
- Kode atau nama klasifikasi DDC.

## Detail Buku

**Tujuan:** Menampilkan metadata dan ketersediaan sebuah buku.

**Aktor:** Pengunjung umum.

**Alur:** Pengunjung memilih buku dari katalog, sistem melakukan route model
binding, lalu menampilkan detail buku.

**Route dan kode terkait:**

- `GET /buku/{book:slug}`
- `BookController@show`
- `resources/views/books/show.blade.php`

## Kategori Buku

**Tujuan:** Menampilkan daftar kategori buku untuk memudahkan navigasi berdasarkan subjek.

**Aktor:** Pengunjung umum.

**Alur:** Pengunjung membuka halaman kategori, sistem menampilkan semua kategori yang tersedia. Pengunjung dapat memilih kategori untuk melihat buku-buku di dalamnya.

**Route dan kode terkait:**

- `GET /kategori`
- `GET /kategori/{category:slug}`
- `CategoryController@index`
- `CategoryController@show`

## Login

**Tujuan:** Mengautentikasi admin dan pengguna dengan satu halaman login.

**Aktor:** Admin dan user aktif.

**Alur:** Pengguna memasukkan email/NISN dan password, sistem memvalidasi
status akun, melakukan rate limiting, membuat session, lalu mengarahkan:

- role `admin` ke `/admin`;
- role `user` ke `/user`.

**Route dan kode terkait:**

- `GET /login`
- `POST /login`
- `AuthenticatedSessionController`
- `LoginRequest`

![Halaman login SIPUS](../public/images/Login.jpg)

## Registrasi Pengguna

**Tujuan:** Memungkinkan siswa mendaftarkan akun.

**Aktor:** Pengunjung yang belum memiliki akun.

**Alur:** Siswa mengisi nama lengkap, NISN, email, kelas, telepon, dan
password. Akun dibuat dengan role `user` serta status `pending` sampai
disetujui admin.

**Route dan kode terkait:**

- `GET /register`
- `POST /register`
- `RegisterUserController`
- `RegisterRequest`

## Lupa Password

**Tujuan:** Mengirim tautan atur ulang kata sandi ke email pengguna.

**Aktor:** Pengguna yang lupa kata sandi.

**Alur:** Pengguna mengisi email pada halaman lupa password, sistem memvalidasi
email, mengirim tautan reset melalui broker password Laravel, lalu menampilkan
pesan sukses jika pengiriman berhasil.

**Route dan kode terkait:**

- `GET|POST /forgot-password`
- `PasswordResetLinkController`
- `PasswordResetLinkRequest`

## Reset Password

**Tujuan:** Mengganti kata sandi melalui token reset yang dikirim ke email.

**Aktor:** Pengguna yang menerima tautan reset password.

**Alur:** Pengguna membuka tautan reset, sistem menampilkan form dengan token
dan email. Setelah pengguna mengirim kata sandi baru yang valid, sistem
memperbarui password, memperbarui `remember_token`, memicu event
`PasswordReset`, lalu mengarahkan pengguna kembali ke login.

**Route dan kode terkait:**

- `GET /reset-password/{token}`
- `POST /reset-password`
- `NewPasswordController`
- `NewPasswordRequest`

## Profil Pengguna pada Panel Filament

**Tujuan:** Menyediakan pengelolaan profil yang aman pada panel admin dan user.

**Aktor:** Admin aktif serta user aktif yang sudah disetujui.

**Alur:** Kedua panel Filament menggunakan halaman profil kustom
`App\Filament\Pages\Auth\EditProfile`. Setiap penyimpanan perubahan mewajibkan
kata sandi saat ini. Jika kata sandi diganti, sistem mengirim notifikasi email
bahwa perubahan berhasil dilakukan. Jika email diganti, Filament menahan email
lama sampai proses verifikasi selesai.

**Batasan khusus user biasa:**

- User hanya dapat mengganti kata sandi dari halaman profil.
- Nama, email, NISN, kelas, dan nomor telepon tidak dapat diubah sendiri.
- Form profil menampilkan instruksi agar perubahan data identitas dilakukan
  melalui admin.

**Kode terkait:**

- `app/Filament/Pages/Auth/EditProfile.php`
- `app/Notifications/PasswordChangedNotification.php`
- `AdminPanelProvider`
- `UserPanelProvider`

## Kustomisasi Reset Password

**Tujuan:** Menyematkan email pengguna ke dalam tautan reset password tanpa mengharuskan pengguna mengisi ulang.

**Alur:** `AppServiceProvider` mendaftarkan callback `ResetPassword::createUrlUsing`. Saat tautan reset dibuat, email pengguna dienkripsi dengan `Crypt::encryptString` dan ditambahkan sebagai parameter `identity` pada URL. Saat pengguna membuka tautan, form reset membaca parameter tersebut dan mendekripsi email untuk diisi otomatis.

**Kode terkait:**
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/Auth/NewPasswordController.php`

## Middleware RedirectToPublicLogin

**Tujuan:** Mengarahkan pengguna yang belum login ke halaman login publik saat mengakses panel Filament, serta menampilkan 404 jika pengguna tidak memiliki akses ke panel tertentu.

**Aktor:** Semua pengguna yang mencoba mengakses `/admin` atau `/user`.

**Alur:** Middleware memperluas `Filament\Http\Middleware\Authenticate`. Jika pengguna sudah login tetapi tidak memiliki akses ke panel (role tidak cocok atau status akun tidak sesuai), middleware mengembalikan 404. Jika pengguna belum login, middleware mengarahkan ke rute `login`.

**Kode terkait:**
- `app/Http/Middleware/RedirectToPublicLogin.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/UserPanelProvider.php`

## Penanganan Error (404 Not Found)

**Tujuan:** Memberikan umpan balik visual yang ramah ketika pengunjung atau pengguna mengakses rute atau data yang tidak tersedia.

**Aktor:** Semua pengunjung.

**Alur:** Pengguna mengakses URL yang tidak terdaftar, sistem menangkap exception lalu merender halaman kustom 404, serta menyediakan tombol untuk kembali ke beranda.

**Route dan kode terkait:**

- Tampilan pada `resources/views/errors/404.blade.php`

## Panel Admin

**Tujuan:** Menyediakan area operasional perpustakaan.

**Aktor:** Admin aktif dan disetujui.

**Path:** `/admin`

![Dashboard admin](../public/images/dashboard-admin.png)

### CRUD Buku

Mengelola identitas buku, ISBN, kategori, DDC, penulis, penerbit, stok, dan
informasi pendukung koleksi.

- Resource: `BookResource`
- Path: `/admin/books`

![CRUD buku](../public/images/crud-buku.png)

### CRUD Kategori

Mengelola pengelompokan umum koleksi buku.

- Resource: `CategoryResource`
- Path: `/admin/categories`

### CRUD DDC

Mengelola klasifikasi Dewey Decimal Classification untuk katalog.

- Resource: `DdcResource`
- Path: `/admin/ddcs`

### CRUD Penulis

Mengelola data penulis buku yang ada di koleksi perpustakaan.

- Resource: `AuthorResource`
- Path: `/admin/authors`

### CRUD Penerbit

Mengelola entitas penerbit buku yang ada di koleksi perpustakaan.

- Resource: `PublisherResource`
- Path: `/admin/publishers`

### CRUD Pengguna

Mengelola identitas, role, status persetujuan, kelas, dan status aktif akun.

- Resource: `UserResource`
- Path: `/admin/users`

### CRUD Peminjaman

Mengelola transaksi peminjaman, anggota, tanggal pinjam/jatuh tempo, item
buku, dan status transaksi. Satu peminjaman (`Loan`) dapat memiliki banyak
item buku (`LoanItem`) yang masing-masing mencatat buku, jumlah, serta
status pengembalian (`returned_at`, `condition_on_return`).

- Resource: `LoanResource`
- Path: `/admin/loans`

### CRUD Denda

Mengelola denda yang terkait dengan transaksi peminjaman.

- Resource: `FineResource`
- Path: `/admin/fines`

### Manajemen Pengembalian

Mengelola proses pengembalian buku dari transaksi peminjaman.

- Resource: `PengembalianResource`
- Path: `/admin/pengembalians`

### Export Laporan Excel

Mengunduh data resource ke file Excel (.xlsx) dengan filter rentang waktu.

**Aktor:** Admin aktif.

**Fitur per resource:**
- **Loans** — `LoansExport`, tombol toolbar di halaman daftar peminjaman
- **Books** — `BooksExport`, tombol toolbar di halaman daftar buku
- **Fines** — `FinesExport`, tombol toolbar di halaman daftar denda
- **Users** — `UsersExport`, tombol toolbar di halaman daftar pengguna

**Filter waktu:** Semua, 1 Minggu, 1 Bulan, 3 Bulan, 6 Bulan, atau kustom via DatePicker.

**Kode terkait:**
- `app/Exports/LoansExport.php`
- `app/Exports/BooksExport.php`
- `app/Exports/FinesExport.php`
- `app/Exports/UsersExport.php`

### Widget Dashboard Admin

Dashboard admin menampilkan widget kustom menggantikan widget bawaan Filament.

- `AdminStatsOverview` — 5 kartu statistik: Total Buku, Anggota Aktif, Peminjaman Aktif, Perlu Disetujui, Denda Belum Dibayar
- `PendingUsersWidget` — Tabel 5 anggota terbaru dengan status `pending`, tombol aksi menuju halaman edit pengguna

**Kode terkait:**
- `app/Filament/Admin/Widgets/AdminStatsOverview.php`
- `app/Filament/Admin/Widgets/PendingUsersWidget.php`

## Panel User

**Tujuan:** Menjadi area layanan khusus pengguna.

**Aktor:** User aktif dan disetujui.

**Path:** `/user`

Panel user saat ini sudah menyediakan:

- Riwayat peminjaman pribadi melalui resource `LoanResource` pada path
  `/user/loans`.
- Daftar denda pribadi melalui resource `FineResource` pada path
  `/user/fines`.
- Halaman profil kustom untuk mengganti kata sandi secara mandiri.
- Dashboard dengan widget kustom:
  - `MyActiveLoansWidget` — tabel peminjaman yang sedang berlangsung (status borrowed/overdue), maksimal 5 baris
  - `MyFinesWidget` — kartu total denda yang belum dibayar

User tidak dapat membuat, mengubah, atau menghapus data peminjaman dan denda
dari panel ini. Resource hanya menampilkan data milik user yang sedang login.

**Kode terkait widget:**
- `app/Filament/User/Widgets/MyActiveLoansWidget.php`
- `app/Filament/User/Widgets/MyFinesWidget.php`

## Authorization dan Policy

Model `User` menerapkan `FilamentUser::canAccessPanel()`:

- Admin hanya dapat membuka panel `admin`.
- User hanya dapat membuka panel `user`.
- Akun pending, rejected, suspended, atau nonaktif ditolak.

Policy tersedia untuk:

- Author
- Book
- Category
- DDC
- Fine
- Loan
- Publisher
- User

Policy melindungi operasi `viewAny`, `view`, `create`, `update`, `delete`,
`restore`, dan `forceDelete` sesuai kebutuhan model.


