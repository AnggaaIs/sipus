# Report Export Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add PDF and XLSX export functionality to 4 Filament admin resources (Loans, Books, Fines, Users).

**Architecture:** Each resource table gets an "Export" header action with a modal to choose PDF or XLSX format. Export is generated synchronously in-memory and streamed directly to browser — zero additional database tables.

**Tech Stack:** Filament v5 Tables Action, barryvdh/laravel-dompdf (PDF), maatwebsite/laravel-excel (XLSX)

---

### Task 1: Install Package

**Files:**
- Modify: `composer.json`

- [ ] **Step 1: Install maatwebsite/laravel-excel**

Run:
```bash
composer require maatwebsite/laravel-excel
```

Expected: Package installed with its dependencies (phpoffice/phpspreadsheet).

- [ ] **Step 2: Verify package is registered**

Run:
```bash
composer show maatwebsite/laravel-excel
```

Expected: Package version displayed.

- [ ] **Step 3: Commit**

```bash
git add composer.json composer.lock
git commit -m "feat: add maatwebsite/laravel-excel dependency"
```

---

### Task 2: Create Loans Export

**Files:**
- Create: `app/Exports/LoansExport.php`
- Create: `resources/views/pdf/loans.blade.php`

- [ ] **Step 1: Create the Loans export class**

```php
<?php

namespace App\Exports;

use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoansExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Loan::with('user', 'loanItems.book')
            ->get()
            ->map(fn (Loan $loan) => (object) [
                'loan_code' => $loan->loan_code,
                'borrower_name' => $loan->user->name,
                'borrower_nisn' => $loan->user->nisn,
                'loan_date' => $loan->loan_date?->format('d/m/Y H:i'),
                'due_date' => $loan->due_date?->format('d/m/Y'),
                'returned_at' => $loan->returned_at?->format('d/m/Y H:i') ?? '-',
                'status' => Loan::statusLabel($loan->resolvedStatus()),
                'books' => $loan->loanItems->map(fn ($i) => $i->book->title)->implode(', '),
            ]);
    }

    public function headings(): array
    {
        return [
            'Kode Pinjam', 'Peminjam', 'NISN', 'Tanggal Pinjam',
            'Jatuh Tempo', 'Dikembalikan', 'Status', 'Buku',
        ];
    }

    /** @param object $row */
    public function map($row): array
    {
        return [
            $row->loan_code,
            $row->borrower_name,
            $row->borrower_nisn,
            $row->loan_date,
            $row->due_date,
            $row->returned_at,
            $row->status,
            $row->books,
        ];
    }

    public static function pdf(): mixed
    {
        $data = (new self)->collection();

        return Pdf::loadView('pdf.loans', ['loans' => $data])
            ->download('laporan-peminjaman.pdf');
    }

    public static function xlsx(): mixed
    {
        return (new self)->downloadXlsx();
    }

    private function downloadXlsx(): mixed
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            $this,
            'laporan-peminjaman.xlsx',
        );
    }
}
```

- [ ] **Step 2: Create the PDF Blade template**

```blade
{{-- resources/views/pdf/loans.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 11px; }
        td { font-size: 10px; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode Pinjam</th>
                <th>Peminjam</th>
                <th>NISN</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Dikembalikan</th>
                <th>Status</th>
                <th>Buku</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
            <tr>
                <td>{{ $loan->loan_code }}</td>
                <td>{{ $loan->borrower_name }}</td>
                <td>{{ $loan->borrower_nisn }}</td>
                <td>{{ $loan->loan_date }}</td>
                <td>{{ $loan->due_date }}</td>
                <td>{{ $loan->returned_at }}</td>
                <td>{{ $loan->status }}</td>
                <td>{{ $loan->books }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
```

- [ ] **Step 3: Commit**

```bash
git add app/Exports/LoansExport.php resources/views/pdf/loans.blade.php
git commit -m "feat: add loans export (PDF + XLSX)"
```

---

### Task 3: Create Books Export

**Files:**
- Create: `app/Exports/BooksExport.php`
- Create: `resources/views/pdf/books.blade.php`

- [ ] **Step 1: Create the Books export class**

```php
<?php

namespace App\Exports;

use App\Models\Book;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Book::with('authors', 'category', 'ddc', 'publisher')
            ->get()
            ->map(fn (Book $book) => (object) [
                'isbn' => $book->isbn,
                'title' => $book->title,
                'authors' => $book->authors->pluck('name')->implode(', '),
                'category' => $book->category?->name ?? '-',
                'ddc' => $book->ddc ? "{$book->ddc->code} - {$book->ddc->name}" : '-',
                'publisher' => $book->publisher?->name ?? '-',
                'year' => $book->publish_year ?? '-',
                'total_copies' => $book->total_copies,
                'available_copies' => $book->available_copies,
            ]);
    }

    public function headings(): array
    {
        return [
            'ISBN', 'Judul', 'Penulis', 'Kategori', 'DDC',
            'Penerbit', 'Tahun', 'Total', 'Tersedia',
        ];
    }

    /** @param object $row */
    public function map($row): array
    {
        return [
            $row->isbn, $row->title, $row->authors, $row->category,
            $row->ddc, $row->publisher, $row->year,
            $row->total_copies, $row->available_copies,
        ];
    }

    public static function pdf(): mixed
    {
        $data = (new self)->collection();

        return Pdf::loadView('pdf.books', ['books' => $data])
            ->download('laporan-buku.pdf');
    }

    public static function xlsx(): mixed
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new self,
            'laporan-buku.xlsx',
        );
    }
}
```

- [ ] **Step 2: Create the PDF Blade template**

```blade
{{-- resources/views/pdf/books.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Buku</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 11px; }
        td { font-size: 10px; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Buku</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Kategori</th>
                <th>DDC</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Total</th>
                <th>Tersedia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $book)
            <tr>
                <td>{{ $book->isbn }}</td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->authors }}</td>
                <td>{{ $book->category }}</td>
                <td>{{ $book->ddc }}</td>
                <td>{{ $book->publisher }}</td>
                <td>{{ $book->year }}</td>
                <td>{{ $book->total_copies }}</td>
                <td>{{ $book->available_copies }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
```

- [ ] **Step 3: Commit**

```bash
git add app/Exports/BooksExport.php resources/views/pdf/books.blade.php
git commit -m "feat: add books export (PDF + XLSX)"
```

---

### Task 4: Create Fines Export

**Files:**
- Create: `app/Exports/FinesExport.php`
- Create: `resources/views/pdf/fines.blade.php`

- [ ] **Step 1: Create the Fines export class**

```php
<?php

namespace App\Exports;

use App\Models\Fine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Fine::with('user', 'loan')
            ->get()
            ->map(fn (Fine $fine) => (object) [
                'loan_code' => $fine->loan?->loan_code ?? '-',
                'user_name' => $fine->user->name,
                'user_nisn' => $fine->user->nisn,
                'overdue_days' => $fine->overdue_days,
                'amount_per_day' => number_format($fine->amount_per_day, 0, ',', '.'),
                'total_amount' => number_format($fine->total_amount, 0, ',', '.'),
                'status' => $fine->status === 'paid' ? 'Lunas' : 'Belum dibayar',
                'paid_at' => $fine->paid_at?->format('d/m/Y H:i') ?? '-',
            ]);
    }

    public function headings(): array
    {
        return [
            'Kode Pinjam', 'Siswa', 'NISN', 'Hari Telat',
            'Denda/Hari', 'Total Denda', 'Status', 'Dibayar Pada',
        ];
    }

    /** @param object $row */
    public function map($row): array
    {
        return [
            $row->loan_code, $row->user_name, $row->user_nisn,
            $row->overdue_days, $row->amount_per_day, $row->total_amount,
            $row->status, $row->paid_at,
        ];
    }

    public static function pdf(): mixed
    {
        $data = (new self)->collection();

        return Pdf::loadView('pdf.fines', ['fines' => $data])
            ->download('laporan-denda.pdf');
    }

    public static function xlsx(): mixed
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new self,
            'laporan-denda.xlsx',
        );
    }
}
```

- [ ] **Step 2: Create the PDF Blade template**

```blade
{{-- resources/views/pdf/fines.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Denda</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 11px; }
        td { font-size: 10px; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Denda</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode Pinjam</th>
                <th>Siswa</th>
                <th>NISN</th>
                <th>Hari Telat</th>
                <th>Denda/Hari</th>
                <th>Total Denda</th>
                <th>Status</th>
                <th>Dibayar Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fines as $fine)
            <tr>
                <td>{{ $fine->loan_code }}</td>
                <td>{{ $fine->user_name }}</td>
                <td>{{ $fine->user_nisn }}</td>
                <td>{{ $fine->overdue_days }}</td>
                <td>Rp {{ $fine->amount_per_day }}</td>
                <td>Rp {{ $fine->total_amount }}</td>
                <td>{{ $fine->status }}</td>
                <td>{{ $fine->paid_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
```

- [ ] **Step 3: Commit**

```bash
git add app/Exports/FinesExport.php resources/views/pdf/fines.blade.php
git commit -m "feat: add fines export (PDF + XLSX)"
```

---

### Task 5: Create Users Export

**Files:**
- Create: `app/Exports/UsersExport.php`
- Create: `resources/views/pdf/users.blade.php`

- [ ] **Step 1: Create the Users export class**

```php
<?php

namespace App\Exports;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return User::query()
            ->get()
            ->map(fn (User $user) => (object) [
                'nisn' => $user->nisn ?? '-',
                'name' => $user->name,
                'full_name' => $user->full_name ?? '-',
                'email' => $user->email,
                'class' => $user->class ?? '-',
                'role' => $user->role === 'admin' ? 'Admin' : 'Siswa',
                'account_status' => match ($user->account_status) {
                    'active' => 'Aktif',
                    'pending' => 'Menunggu',
                    'rejected' => 'Ditolak',
                    'suspended' => 'Ditangguhkan',
                    default => $user->account_status,
                },
                'approved_at' => $user->approved_at?->format('d/m/Y H:i') ?? '-',
                'phone' => $user->phone ?? '-',
            ]);
    }

    public function headings(): array
    {
        return [
            'NISN', 'Username', 'Nama Lengkap', 'Email', 'Kelas',
            'Peran', 'Status', 'Disetujui Pada', 'Telepon',
        ];
    }

    /** @param object $row */
    public function map($row): array
    {
        return [
            $row->nisn, $row->name, $row->full_name, $row->email,
            $row->class, $row->role, $row->account_status,
            $row->approved_at, $row->phone,
        ];
    }

    public static function pdf(): mixed
    {
        $data = (new self)->collection();

        return Pdf::loadView('pdf.users', ['users' => $data])
            ->download('laporan-pengguna.pdf');
    }

    public static function xlsx(): mixed
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new self,
            'laporan-pengguna.xlsx',
        );
    }
}
```

- [ ] **Step 2: Create the PDF Blade template**

```blade
{{-- resources/views/pdf/users.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pengguna</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 11px; }
        td { font-size: 10px; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Pengguna</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>NISN</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Kelas</th>
                <th>Peran</th>
                <th>Status</th>
                <th>Disetujui Pada</th>
                <th>Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->nisn }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->class }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->account_status }}</td>
                <td>{{ $user->approved_at }}</td>
                <td>{{ $user->phone }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
```

- [ ] **Step 3: Commit**

```bash
git add app/Exports/UsersExport.php resources/views/pdf/users.blade.php
git commit -m "feat: add users export (PDF + XLSX)"
```

---

### Task 6: Add Export Actions to Table Classes

**Files:**
- Modify: `app/Filament/Admin/Resources/Loans/Tables/LoansTable.php`
- Modify: `app/Filament/Admin/Resources/Books/Tables/BooksTable.php`
- Modify: `app/Filament/Admin/Resources/Fines/Tables/FinesTable.php`
- Modify: `app/Filament/Admin/Resources/Users/Tables/UsersTable.php`

- [ ] **Step 1: Add export action to LoansTable**

Add the import for `Action` and the export action in the toolbar:

```php
<?php

namespace App\Filament\Admin\Resources\Loans\Tables;

use App\Exports\LoansExport;
use App\Models\Loan;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable(),
                TextColumn::make('loan_code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('loanItems.book.title')
                    ->label('Buku')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
                TextColumn::make('loan_date')
                    ->label('Tanggal pinjam')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->label('Dikembalikan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Loan $record): string => $record->resolvedStatus())
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Loan::statusLabel($state))
                    ->colors([
                        'info' => Loan::STATUS_BORROWED,
                        'success' => Loan::STATUS_RETURNED,
                        'danger' => Loan::STATUS_OVERDUE,
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Loan::statusOptions())
                    ->query(function (Builder $query, array $data): void {
                        match ($data['value'] ?? null) {
                            Loan::STATUS_BORROWED => $query->currentlyBorrowed(),
                            Loan::STATUS_RETURNED => $query->returned(),
                            Loan::STATUS_OVERDUE => $query->currentlyOverdue(),
                            default => null,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Select::make('format')
                            ->label('Format')
                            ->options([
                                'pdf' => 'PDF',
                                'xlsx' => 'Excel (XLSX)',
                            ])
                            ->required(),
                    ])
                    ->action(fn (array $data) => match ($data['format']) {
                        'pdf' => LoansExport::pdf(),
                        'xlsx' => LoansExport::xlsx(),
                    }),
            ]);
    }
}
```

- [ ] **Step 2: Add export action to BooksTable**

Modify the `toolbarActions` section of `BooksTable.php` to add the export action:

```php
use App\Exports\BooksExport;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;

// Inside configure(), replace ->toolbarActions([...]) with:
            ->toolbarActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Select::make('format')
                            ->label('Format')
                            ->options([
                                'pdf' => 'PDF',
                                'xlsx' => 'Excel (XLSX)',
                            ])
                            ->required(),
                    ])
                    ->action(fn (array $data) => match ($data['format']) {
                        'pdf' => BooksExport::pdf(),
                        'xlsx' => BooksExport::xlsx(),
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
```

- [ ] **Step 3: Add export action to FinesTable**

Modify the `toolbarActions` section of `FinesTable.php`:

```php
use App\Exports\FinesExport;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;

// Replace ->toolbarActions([]) with:
            ->toolbarActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Select::make('format')
                            ->label('Format')
                            ->options([
                                'pdf' => 'PDF',
                                'xlsx' => 'Excel (XLSX)',
                            ])
                            ->required(),
                    ])
                    ->action(fn (array $data) => match ($data['format']) {
                        'pdf' => FinesExport::pdf(),
                        'xlsx' => FinesExport::xlsx(),
                    }),
            ]);
```

- [ ] **Step 4: Add export action to UsersTable**

Modify the `toolbarActions` section of `UsersTable.php`:

```php
use App\Exports\UsersExport;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;

// Replace ->toolbarActions([BulkActionGroup::make([...])]) with:
            ->toolbarActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Select::make('format')
                            ->label('Format')
                            ->options([
                                'pdf' => 'PDF',
                                'xlsx' => 'Excel (XLSX)',
                            ])
                            ->required(),
                    ])
                    ->action(fn (array $data) => match ($data['format']) {
                        'pdf' => UsersExport::pdf(),
                        'xlsx' => UsersExport::xlsx(),
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
```

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Admin/Resources/Loans/Tables/LoansTable.php app/Filament/Admin/Resources/Books/Tables/BooksTable.php app/Filament/Admin/Resources/Fines/Tables/FinesTable.php app/Filament/Admin/Resources/Users/Tables/UsersTable.php
git commit -m "feat: add export action buttons to resource tables"
```

---

### Task 7: Run Format & Verify

- [ ] **Step 1: Run Pint to fix formatting**

```bash
vendor/bin/pint --format agent
```

- [ ] **Step 2: Run existing tests to make sure nothing broke**

```bash
php artisan test --compact
```

Expected: All existing tests pass.

- [ ] **Step 3: Commit formatting fixes (if any)**

```bash
git add -A
git commit -m "style: apply pint formatting"
```

---

### Task 8: Write Export Tests

**Files:**
- Create: `tests/Feature/ExportFeatureTest.php`

- [ ] **Step 1: Create the test file**

```bash
php artisan make:test --pest ExportFeatureTest
```

- [ ] **Step 2: Write tests for export feature**

```php
<?php

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    actingAs($this->admin);
});

test('halaman index loans dapat diakses', function () {
    get('/admin/loans')->assertOk();
});

test('halaman index books dapat diakses', function () {
    get('/admin/books')->assertOk();
});

test('halaman index fines dapat diakses', function () {
    get('/admin/fines')->assertOk();
});

test('halaman index users dapat diakses', function () {
    get('/admin/users')->assertOk();
});

test('tombol export muncul di halaman loans', function () {
    $response = get('/admin/loans');
    $response->assertSee('Export');
});

test('tombol export muncul di halaman books', function () {
    $response = get('/admin/books');
    $response->assertSee('Export');
});

test('tombol export muncul di halaman fines', function () {
    $response = get('/admin/fines');
    $response->assertSee('Export');
});

test('tombol export muncul di halaman users', function () {
    $response = get('/admin/users');
    $response->assertSee('Export');
});

test('loans export class menghasilkan data dengan struktur yang benar', function () {
    $user = User::factory()->member()->create();
    $book = Book::factory()->create();
    $loan = Loan::factory()->create(['user_id' => $user->getKey()]);
    $loan->loanItems()->create(['book_id' => $book->getKey(), 'quantity' => 1]);

    $export = new \App\Exports\LoansExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['loan_code', 'borrower_name', 'status']);
});

test('books export class menghasilkan data dengan struktur yang benar', function () {
    $book = Book::factory()->create();

    $export = new \App\Exports\BooksExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['isbn', 'title', 'authors', 'total_copies']);
});

test('fines export class menghasilkan data dengan struktur yang benar', function () {
    $user = User::factory()->member()->create();
    $loan = Loan::factory()->create(['user_id' => $user->getKey()]);
    Fine::factory()->create([
        'loan_id' => $loan->getKey(),
        'user_id' => $user->getKey(),
    ]);

    $export = new \App\Exports\FinesExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['loan_code', 'user_name', 'total_amount']);
});

test('users export class menghasilkan data dengan struktur yang benar', function () {
    User::factory()->member()->create();

    $export = new \App\Exports\UsersExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['nisn', 'name', 'email', 'role']);
});
```

- [ ] **Step 3: Run the test**

```bash
php artisan test --compact --filter=ExportFeatureTest
```

Expected: All tests pass.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/ExportFeatureTest.php
git commit -m "test: add export feature tests"
```

---

### Task 9: Update PPKPL Documentation

**Files:**
- Modify: `ppkpl/Laporan_Pengujian_PPKPL_SIPUS.md`
- Modify: `ppkpl/Bukti_Eksekusi_Automated_Testing.md`

- [ ] **Step 1: Update Laporan_Pengujian**

Add new test cases (TC-40 to TC-47) covering export:
- TC-40: Export PDF peminjaman
- TC-41: Export XLSX peminjaman
- TC-42: Export PDF buku
- TC-43: Export XLSX buku
- TC-44: Export PDF denda
- TC-45: Export XLSX denda
- TC-46: Export PDF pengguna
- TC-47: Export XLSX pengguna

Update feature list table to include "Export Laporan".

- [ ] **Step 2: Update Bukti_Eksekusi**

Record the test execution results for the new export tests.

- [ ] **Step 3: Commit**

```bash
git add ppkpl/
git commit -m "docs: update ppkpl testing documentation"
```
