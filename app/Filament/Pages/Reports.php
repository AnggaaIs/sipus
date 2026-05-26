<?php

namespace App\Filament\Pages;

use App\Services\Reports\LibraryReportData;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Laporan';

    protected static ?int $navigationSort = 90;

    protected static ?string $slug = 'laporan';

    protected string $view = 'filament.pages.reports';

    public string $reportType = 'summary';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate ??= now()->startOfMonth()->toDateString();
        $this->endDate ??= now()->toDateString();
    }

    public function reportData(): LibraryReportData
    {
        return new LibraryReportData(
            reportType: $this->reportType,
            startDate: $this->startDate,
            endDate: $this->endDate,
        );
    }

    /**
     * @return array<string, string>
     */
    public function reportTypeOptions(): array
    {
        return $this->reportData()->reportTypeOptions();
    }

    public function reportTitle(): string
    {
        return $this->reportData()->reportTitle();
    }

    public function dateRangeLabel(): string
    {
        return $this->reportData()->dateRangeLabel();
    }

    public function printUrl(): string
    {
        $report = $this->reportData();

        return route('admin.reports.print', [
            'jenis' => $report->type(),
            'dari' => $report->startDateValue(),
            'sampai' => $report->endDateValue(),
        ]);
    }
}
