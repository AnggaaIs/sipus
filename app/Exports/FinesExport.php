<?php

namespace App\Exports;

use App\Models\Fine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

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
        return Excel::download(
            new self,
            'laporan-denda.xlsx',
        );
    }
}
