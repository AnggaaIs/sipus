<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

abstract class BaseExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
    ) {}

    abstract protected function title(): string;

    abstract protected function fileName(): string;

    abstract protected function columnCount(): int;

    abstract public function collection(): Collection;

    abstract public function headings(): array;

    abstract public function map($row): array;

    protected function statusColumn(): ?string
    {
        return null;
    }

    protected function statusColors(): array
    {
        return [];
    }

    protected function afterSheet(AfterSheet $event, string $lastCol, int $lastRow): void {}

    public function registerEvents(): array
    {
        $lastCol = chr(64 + $this->columnCount());
        $statusCol = $this->statusColumn();

        $range = 'Semua data';
        if ($this->startDate && $this->endDate) {
            $range = $this->startDate.' s.d. '.$this->endDate;
        }

        return [
            AfterSheet::class => function (AfterSheet $event) use ($lastCol, $range, $statusCol): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 2);

                $sheet->setCellValue('A1', $this->title());
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A2', 'Periode: '.$range);
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

                $headerRange = "A3:{$lastCol}3";
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4472C4');
                $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $lastRow = $sheet->getHighestRow();
                $dataRange = "A4:{$lastCol}{$lastRow}";
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                if ($statusCol && ! empty($this->statusColors()) && $lastRow >= 4) {
                    for ($row = 4; $row <= $lastRow; $row++) {
                        $cellValue = $sheet->getCell("{$statusCol}{$row}")->getValue();
                        $color = $this->statusColors()[$cellValue] ?? null;

                        if ($color) {
                            $sheet->getStyle("{$statusCol}{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($color);
                        }
                    }
                }

                $this->afterSheet($event, $lastCol, $lastRow);
            },
        ];
    }

    public static function xlsx(?string $startDate = null, ?string $endDate = null): mixed
    {
        $filename = (new static($startDate, $endDate))->fileName();

        if ($startDate && $endDate) {
            $filename .= '_'.$startDate.'_'.$endDate;
        }

        return Excel::download(
            new static($startDate, $endDate),
            $filename.'.xlsx',
        );
    }
}
