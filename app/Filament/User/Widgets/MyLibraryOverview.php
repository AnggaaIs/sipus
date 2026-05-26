<?php

namespace App\Filament\User\Widgets;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyLibraryOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan Aktivitas Saya';

    protected ?string $description = 'Pantauan cepat pinjaman, pengembalian, dan denda Anda.';

    /**
     * @var int | array<string, int | null> | null
     */
    protected int|array|null $columns = [
        'md' => 2,
        'xl' => 4,
    ];

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || blank($user->getKey())) {
            return $this->getEmptyStats();
        }

        $userId = $user->id;
        $monthlyLoans = $this->getMonthlyLoanCounts($userId);
        $monthlyReturns = $this->getMonthlyReturnCounts($userId);
        $monthlyUnpaidFines = $this->getMonthlyUnpaidFineCounts($userId);
        $overdueLoans = Loan::query()
            ->where('user_id', $userId)
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
        $loansDueSoon = Loan::query()
            ->where('user_id', $userId)
            ->where('status', 'borrowed')
            ->whereDate('due_date', '>=', today())
            ->whereDate('due_date', '<=', today()->addDays(3))
            ->count('id');
        $returnedThisMonth = Loan::query()
            ->where('user_id', $userId)
            ->where('status', 'returned')
            ->whereBetween('returned_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count('id');
        $unpaidFineCount = Fine::query()
            ->where('user_id', $userId)
            ->where('status', 'unpaid')
            ->count('id');
        $unpaidFineAmount = (float) Fine::query()
            ->where('user_id', $userId)
            ->where('status', 'unpaid')
            ->sum('total_amount');

        return [
            Stat::make('Pinjaman Aktif Saya', number_format(
                Loan::query()
                    ->where('user_id', $userId)
                    ->where('status', 'borrowed')
                    ->count('id'),
            ))
                ->description($overdueLoans > 0 ? number_format($overdueLoans).' sudah melewati jatuh tempo' : 'Tidak ada pinjaman yang terlambat')
                ->descriptionIcon('heroicon-m-book-open')
                ->chart($monthlyLoans)
                ->color('primary'),
            Stat::make('Peminjaman Bulan Ini', number_format(
                Loan::query()
                    ->where('user_id', $userId)
                    ->whereBetween('loan_date', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count('id'),
            ))
                ->description($loansDueSoon > 0 ? number_format($loansDueSoon).' akan jatuh tempo dalam 3 hari' : 'Belum ada pinjaman yang mendekati jatuh tempo')
                ->descriptionIcon('heroicon-m-clock')
                ->chart($monthlyLoans)
                ->color('success'),
            Stat::make('Riwayat Pengembalian', number_format(
                Loan::query()
                    ->where('user_id', $userId)
                    ->where('status', 'returned')
                    ->count('id'),
            ))
                ->description(number_format($returnedThisMonth).' buku selesai dikembalikan bulan ini')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->chart($monthlyReturns)
                ->color('info'),
            Stat::make('Denda Belum Lunas', $this->formatCurrency($unpaidFineAmount))
                ->description($unpaidFineCount > 0 ? number_format($unpaidFineCount).' tagihan belum dibayar' : 'Tidak ada denda aktif saat ini')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart($monthlyUnpaidFines)
                ->color('danger'),
        ];
    }

    /**
     * @return array<Stat>
     */
    private function getEmptyStats(): array
    {
        $emptyChart = array_fill(0, 6, 0);

        return [
            Stat::make('Pinjaman Aktif Saya', '0')
                ->description('Belum ada data pinjaman')
                ->descriptionIcon('heroicon-m-book-open')
                ->chart($emptyChart)
                ->color('primary'),
            Stat::make('Peminjaman Bulan Ini', '0')
                ->description('Belum ada aktivitas bulan ini')
                ->descriptionIcon('heroicon-m-clock')
                ->chart($emptyChart)
                ->color('success'),
            Stat::make('Riwayat Pengembalian', '0')
                ->description('Belum ada data pengembalian')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->chart($emptyChart)
                ->color('info'),
            Stat::make('Denda Belum Lunas', $this->formatCurrency(0))
                ->description('Tidak ada denda aktif')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart($emptyChart)
                ->color('danger'),
        ];
    }

    /**
     * @return array<int>
     */
    private function getMonthlyLoanCounts(int $userId): array
    {
        return $this->getMonthlyCounts(
            function ($month) use ($userId): int {
                return Loan::query()
                    ->where('user_id', $userId)
                    ->whereYear('loan_date', $month->year)
                    ->whereMonth('loan_date', $month->month)
                    ->count('id');
            },
        );
    }

    /**
     * @return array<int>
     */
    private function getMonthlyReturnCounts(int $userId): array
    {
        return $this->getMonthlyCounts(
            function ($month) use ($userId): int {
                return Loan::query()
                    ->where('user_id', $userId)
                    ->where('status', 'returned')
                    ->whereYear('returned_at', $month->year)
                    ->whereMonth('returned_at', $month->month)
                    ->count('id');
            },
        );
    }

    /**
     * @return array<int>
     */
    private function getMonthlyUnpaidFineCounts(int $userId): array
    {
        return $this->getMonthlyCounts(
            function ($month) use ($userId): int {
                return Fine::query()
                    ->where('user_id', $userId)
                    ->where('status', 'unpaid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count('id');
            },
        );
    }

    /**
     * @param  callable(object): int  $resolver
     * @return array<int>
     */
    private function getMonthlyCounts(callable $resolver): array
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $data[] = $resolver($month);
        }

        return $data;
    }

    private function formatCurrency(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
