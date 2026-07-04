<?php

namespace App\Exports;

use App\Models\Fine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FinesExport extends BaseExport
{
    protected function title(): string
    {
        return 'Laporan Denda';
    }

    protected function fileName(): string
    {
        return 'laporan-denda';
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
            'Belum dibayar' => 'FFFF4444',
            'Lunas' => 'FF44FF44',
        ];
    }

    protected function afterSheet(AfterSheet $event, string $lastCol, int $lastRow): void
    {
        $event->sheet->getDelegate()
            ->getStyle("E4:F{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }

    public function collection(): Collection
    {
        return Fine::with('user', 'loan')
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->get()
            ->map(fn (Fine $fine) => (object) [
                'loan_code' => $fine->loan?->loan_code ?? '-',
                'user_name' => $fine->user->name,
                'user_nisn' => $fine->user->nisn,
                'overdue_days' => $fine->overdue_days,
                'amount_per_day' => (int) $fine->amount_per_day,
                'total_amount' => (int) $fine->total_amount,
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

    public function map($row): array
    {
        return [
            $row->loan_code, $row->user_name, $row->user_nisn,
            $row->overdue_days, $row->amount_per_day, $row->total_amount,
            $row->status, $row->paid_at,
        ];
    }
}
