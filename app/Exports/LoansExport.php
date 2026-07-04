<?php

namespace App\Exports;

use App\Models\Loan;
use Illuminate\Support\Collection;

class LoansExport extends BaseExport
{
    protected function title(): string
    {
        return 'Laporan Peminjaman Buku';
    }

    protected function fileName(): string
    {
        return 'laporan-peminjaman';
    }

    protected function columnCount(): int
    {
        return 8;
    }

    protected function statusColumn(): ?string
    {
        return 'G';
    }

    protected function statusColors(): array
    {
        return [
            'Terlambat' => 'FFFF4444',
            'Dikembalikan' => 'FF44FF44',
            'Dipinjam' => 'FF4444FF',
        ];
    }

    public function collection(): Collection
    {
        return Loan::with('user', 'loanItems.book')
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate))
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
}
