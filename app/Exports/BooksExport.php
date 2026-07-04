<?php

namespace App\Exports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BooksExport extends BaseExport
{
    protected function title(): string
    {
        return 'Laporan Data Buku';
    }

    protected function fileName(): string
    {
        return 'laporan-buku';
    }

    protected function columnCount(): int
    {
        return 9;
    }

    protected function afterSheet(AfterSheet $event, string $lastCol, int $lastRow): void
    {
        if ($lastRow < 4) {
            return;
        }

        $event->sheet->getDelegate()
            ->getStyle("A4:A{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_TEXT);
    }

    public function collection(): Collection
    {
        return Book::with('authors', 'category', 'ddc', 'publisher')
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate))
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

    public function map($row): array
    {
        return [
            $row->isbn, $row->title, $row->authors, $row->category,
            $row->ddc, $row->publisher, $row->year,
            $row->total_copies, $row->available_copies,
        ];
    }
}
