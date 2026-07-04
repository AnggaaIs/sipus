<?php

namespace App\Exports;

use App\Models\Loan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

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
        return Excel::download(
            new self,
            'laporan-peminjaman.xlsx',
        );
    }
}
