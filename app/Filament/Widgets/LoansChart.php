<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\ChartWidget;

class LoansChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Statistik Peminjaman (6 Bulan Terakhir)';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');

            $count = Loan::whereMonth('loan_date', $month->month)
                ->whereYear('loan_date', $month->year)
                ->count('id');

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => $data,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
