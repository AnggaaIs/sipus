<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class LibraryOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan Perpustakaan';

    protected ?string $description = 'Pantauan cepat koleksi, peminjaman, pengguna, dan denda.';

    /**
     * @var int | array<string, int | null> | null
     */
    protected int|array|null $columns = [
        'md' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $totalBooks = Book::query()->count('id');
        $totalCopies = Book::query()->sum('total_copies');
        $availableCopies = Book::query()->sum('available_copies');
        $activeMembers = User::query()
            ->where('role', 'user')
            ->where('account_status', 'active')
            ->where('is_active', true)
            ->count('id');
        $pendingMembers = User::query()
            ->where('role', 'user')
            ->where('account_status', 'pending')
            ->count('id');
        $activeLoans = Loan::query()->where('status', 'borrowed')->count('id');
        $overdueLoans = Loan::query()
            ->where(function (Builder $query): void {
                $query
                    ->where('status', 'overdue')
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('status', 'borrowed')
                            ->whereDate('due_date', '<', today());
                    });
            })
            ->count('id');
        $returnedThisMonth = Loan::query()
            ->where('status', 'returned')
            ->whereBetween('returned_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count('id');
        $unpaidFines = Fine::query()->where('status', 'unpaid')->count('id');
        $unpaidFineAmount = Fine::query()->where('status', 'unpaid')->sum('total_amount');

        return [
            Stat::make('Judul Buku', number_format($totalBooks))
                ->description(number_format($totalCopies).' eksemplar tercatat')
                ->descriptionIcon('heroicon-m-book-open')
                ->chart([3, 5, 4, 10, 8, 12, 15])
                ->color('primary'),
            Stat::make('Stok Tersedia', number_format($availableCopies))
                ->description('Siap dipinjam di perpustakaan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([8, 9, 7, 10, 12, 11, 13])
                ->color('success'),
            Stat::make('Siswa Aktif', number_format($activeMembers))
                ->description(number_format($pendingMembers).' akun menunggu persetujuan')
                ->descriptionIcon('heroicon-m-users')
                ->chart([2, 3, 5, 4, 7, 8, 9])
                ->color('info'),
            Stat::make('Peminjaman Aktif', number_format($activeLoans))
                ->description('Masih dalam masa pinjam')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->chart([4, 6, 5, 7, 8, 6, 9])
                ->color('warning'),
            Stat::make('Terlambat', number_format($overdueLoans))
                ->description('Perlu segera ditindaklanjuti')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart([1, 2, 1, 3, 2, 4, 3])
                ->color('danger'),
            Stat::make('Pengembalian Bulan Ini', number_format($returnedThisMonth))
                ->description('Buku yang sudah kembali')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->chart([1, 3, 2, 5, 4, 6, 7])
                ->color('success'),
            Stat::make('Denda Belum Lunas', number_format($unpaidFines))
                ->description($this->formatCurrency((float) $unpaidFineAmount).' belum dibayar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->chart([1, 4, 2, 8, 5, 2, 1])
                ->color('danger'),
            Stat::make('Stok Habis', number_format(Book::query()->where('available_copies', 0)->count('id')))
                ->description('Judul yang sementara tidak tersedia')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->chart([0, 1, 0, 2, 1, 3, 2])
                ->color('gray'),
        ];
    }

    private function formatCurrency(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
