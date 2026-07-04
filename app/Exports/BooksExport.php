<?php

namespace App\Exports;

use App\Models\Book;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

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
        return Excel::download(
            new self,
            'laporan-buku.xlsx',
        );
    }
}
