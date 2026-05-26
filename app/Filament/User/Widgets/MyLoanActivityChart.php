<?php

namespace App\Filament\User\Widgets;

use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MyLoanActivityChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Aktivitas Peminjaman 6 Bulan Terakhir';

    protected ?string $description = 'Perbandingan buku yang dipinjam dan dikembalikan setiap bulan.';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || blank($user->getKey())) {
            return [
                'datasets' => [
                    [
                        'label' => 'Dipinjam',
                        'data' => array_fill(0, 6, 0),
                    ],
                    [
                        'label' => 'Dikembalikan',
                        'data' => array_fill(0, 6, 0),
                    ],
                ],
                'labels' => $this->getMonthLabels(),
            ];
        }

        $userId = $user->id;
        $borrowed = [];
        $returned = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $borrowed[] = Loan::query()
                ->where('user_id', $userId)
                ->whereYear('loan_date', $month->year)
                ->whereMonth('loan_date', $month->month)
                ->count('id');

            $returned[] = Loan::query()
                ->where('user_id', $userId)
                ->where('status', 'returned')
                ->whereYear('returned_at', $month->year)
                ->whereMonth('returned_at', $month->month)
                ->count('id');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Dipinjam',
                    'data' => $borrowed,
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Dikembalikan',
                    'data' => $returned,
                    'borderColor' => '#0891b2',
                    'backgroundColor' => 'rgba(8, 145, 178, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $this->getMonthLabels(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string>
     */
    private function getMonthLabels(): array
    {
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $labels[] = now()->subMonths($i)->translatedFormat('M Y');
        }

        return $labels;
    }
}
