<?php

namespace App\Services\Reports;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class LibraryReportData
{
    public function __construct(
        private string $reportType = 'summary',
        private ?string $startDate = null,
        private ?string $endDate = null,
    ) {
        if (! array_key_exists($this->reportType, $this->reportTypeOptions())) {
            $this->reportType = 'summary';
        }

        $this->startDate ??= now()->startOfMonth()->toDateString();
        $this->endDate ??= now()->toDateString();
    }

    /**
     * @return array<string, string>
     */
    public function reportTypeOptions(): array
    {
        return [
            'summary' => 'Ringkasan',
            'books' => 'Buku',
            'loans' => 'Peminjaman',
            'returns' => 'Pengembalian',
            'fines' => 'Denda',
        ];
    }

    public function type(): string
    {
        return $this->reportType;
    }

    public function startDateValue(): string
    {
        return $this->dateRange()[0]->toDateString();
    }

    public function endDateValue(): string
    {
        return $this->dateRange()[1]->toDateString();
    }

    public function reportTitle(): string
    {
        return match ($this->reportType) {
            'books' => 'Laporan Buku',
            'loans' => 'Laporan Peminjaman',
            'returns' => 'Laporan Pengembalian',
            'fines' => 'Laporan Denda',
            default => 'Ringkasan Laporan Perpustakaan',
        };
    }

    public function dateRangeLabel(): string
    {
        [$startDate, $endDate] = $this->dateRange();

        return $startDate->translatedFormat('d F Y').' - '.$endDate->translatedFormat('d F Y');
    }

    public function printedAt(): string
    {
        return now()->translatedFormat('d F Y H:i');
    }

    /**
     * @return array<int, array{label: string, value: string, note: string}>
     */
    public function summaryCards(): array
    {
        $unpaidFineAmount = Fine::query()
            ->where('status', 'unpaid')
            ->sum('total_amount');

        return [
            [
                'label' => 'Judul Buku',
                'value' => number_format(Book::query()->count()),
                'note' => number_format(Book::query()->sum('total_copies')).' eksemplar tercatat',
            ],
            [
                'label' => 'Stok Tersedia',
                'value' => number_format(Book::query()->sum('available_copies')),
                'note' => 'Eksemplar siap dipinjam',
            ],
            [
                'label' => 'Siswa Aktif',
                'value' => number_format($this->activeMemberQuery()->count()),
                'note' => number_format($this->pendingMemberQuery()->count()).' menunggu persetujuan',
            ],
            [
                'label' => 'Peminjaman',
                'value' => number_format($this->loanQuery()->count()),
                'note' => 'Sesuai periode laporan',
            ],
            [
                'label' => 'Pengembalian',
                'value' => number_format($this->returnQuery()->count()),
                'note' => 'Sesuai periode laporan',
            ],
            [
                'label' => 'Denda',
                'value' => number_format($this->fineQuery()->count()),
                'note' => $this->formatCurrency((float) $unpaidFineAmount).' belum lunas',
            ],
        ];
    }

    /**
     * @return Collection<int, Book>
     */
    public function books(): Collection
    {
        return Book::query()
            ->with(['authors', 'category', 'publisher'])
            ->orderBy('title')
            ->get();
    }

    /**
     * @return Collection<int, Loan>
     */
    public function loans(): Collection
    {
        return $this->loanQuery()
            ->with(['user', 'loanItems.book'])
            ->latest('loan_date')
            ->get();
    }

    /**
     * @return Collection<int, Loan>
     */
    public function returns(): Collection
    {
        return $this->returnQuery()
            ->with(['user', 'loanItems.book'])
            ->latest('returned_at')
            ->get();
    }

    /**
     * @return Collection<int, Fine>
     */
    public function fines(): Collection
    {
        return $this->fineQuery()
            ->with(['user', 'loan'])
            ->latest()
            ->get();
    }

    public function userName(?User $user): string
    {
        return $user?->full_name ?: $user?->name ?: '-';
    }

    public function loanBooks(Loan $loan): string
    {
        return $loan->loanItems
            ->map(fn ($loanItem): string => ($loanItem->book?->title ?? 'Buku tidak tersedia').' ('.$loanItem->quantity.')')
            ->join(', ');
    }

    public function returnConditions(Loan $loan): string
    {
        return $loan->loanItems
            ->map(fn ($loanItem): string => match ($loanItem->condition_on_return) {
                'damaged' => 'Rusak',
                'lost' => 'Hilang',
                default => 'Baik',
            })
            ->unique()
            ->join(', ');
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'paid' => 'Lunas',
            'unpaid' => 'Belum dibayar',
            default => $status,
        };
    }

    public function formatCurrency(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function dateRange(): array
    {
        $startDate = $this->parseDate($this->startDate, now()->startOfMonth()->toDateString())->startOfDay();
        $endDate = $this->parseDate($this->endDate, now()->toDateString())->endOfDay();

        if ($startDate->greaterThan($endDate)) {
            return [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        return [$startDate, $endDate];
    }

    private function parseDate(?string $date, string $fallback): Carbon
    {
        try {
            return Carbon::parse($date ?: $fallback);
        } catch (Throwable) {
            return Carbon::parse($fallback);
        }
    }

    /**
     * @return Builder<Loan>
     */
    private function loanQuery(): Builder
    {
        [$startDate, $endDate] = $this->dateRange();

        return Loan::query()
            ->whereBetween('loan_date', [$startDate->toDateString(), $endDate->toDateString()]);
    }

    /**
     * @return Builder<Loan>
     */
    private function returnQuery(): Builder
    {
        [$startDate, $endDate] = $this->dateRange();

        return Loan::query()
            ->where('status', 'returned')
            ->whereBetween('returned_at', [$startDate, $endDate]);
    }

    /**
     * @return Builder<Fine>
     */
    private function fineQuery(): Builder
    {
        [$startDate, $endDate] = $this->dateRange();

        return Fine::query()
            ->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * @return Builder<User>
     */
    private function activeMemberQuery(): Builder
    {
        return User::query()
            ->where('role', 'user')
            ->where('account_status', 'active')
            ->where('is_active', true);
    }

    /**
     * @return Builder<User>
     */
    private function pendingMemberQuery(): Builder
    {
        return User::query()
            ->where('role', 'user')
            ->where('account_status', 'pending');
    }
}
