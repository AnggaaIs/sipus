<?php

namespace App\Filament\User\Widgets;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyLoanStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected ?string $heading = 'Komposisi Status Peminjaman';

    protected ?string $description = 'Gambaran cepat kondisi pinjaman dan denda Anda saat ini.';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '320px';

    protected string $color = 'warning';

    protected function getData(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || blank($user->getKey())) {
            return [
                'datasets' => [
                    [
                        'data' => [0, 0, 0, 0],
                        'backgroundColor' => ['#4f46e5', '#ef4444', '#14b8a6', '#f59e0b'],
                    ],
                ],
                'labels' => ['Aktif', 'Terlambat', 'Selesai', 'Denda belum lunas'],
            ];
        }

        $userId = $user->id;

        return [
            'datasets' => [
                [
                    'data' => [
                        Loan::query()
                            ->where('user_id', $userId)
                            ->where('status', 'borrowed')
                            ->whereDate('due_date', '>=', today())
                            ->count('id'),
                        Loan::query()
                            ->where('user_id', $userId)
                            ->where(function (Builder $query): void {
                                $query
                                    ->where('status', 'overdue')
                                    ->orWhere(function (Builder $query): void {
                                        $query
                                            ->where('status', 'borrowed')
                                            ->whereDate('due_date', '<', today());
                                    });
                            })
                            ->count('id'),
                        Loan::query()
                            ->where('user_id', $userId)
                            ->where('status', 'returned')
                            ->count('id'),
                        Fine::query()
                            ->where('user_id', $userId)
                            ->where('status', 'unpaid')
                            ->count('id'),
                    ],
                    'backgroundColor' => ['#4f46e5', '#ef4444', '#14b8a6', '#f59e0b'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Aktif', 'Terlambat', 'Selesai', 'Denda belum lunas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
            'cutout' => '68%',
        ];
    }
}
