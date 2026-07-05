<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Buku', number_format(Book::count()))
                ->description('Jumlah seluruh buku di perpustakaan')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            Stat::make('Anggota Aktif', number_format(User::where('role', 'user')->where('account_status', 'active')->count()))
                ->description('Anggota terdaftar dan aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Peminjaman Aktif', number_format(Loan::where('status', Loan::STATUS_BORROWED)->count()))
                ->description('Buku sedang dipinjam')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),
            Stat::make('Perlu Disetujui', number_format(User::where('account_status', 'pending')->count()))
                ->description('Anggota menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
            Stat::make('Denda Belum Dibayar', 'Rp '.number_format((float) Fine::where('status', 'unpaid')->sum('total_amount'), 0, ',', '.'))
                ->description('Total denda outstanding')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray'),
        ];
    }
}
