<?php

namespace App\Filament\User\Widgets;

use App\Models\Fine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MyFinesWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalFine = (float) Fine::where('user_id', Auth::id())
            ->where('status', 'unpaid')
            ->sum('total_amount');

        return [
            Stat::make('Total Denda', 'Rp '.number_format($totalFine, 0, ',', '.'))
                ->description($totalFine > 0 ? 'Denda yang belum dibayar' : 'Tidak ada denda')
                ->descriptionIcon($totalFine > 0 ? 'heroicon-m-banknotes' : 'heroicon-m-check-circle')
                ->color($totalFine > 0 ? 'danger' : 'success'),
        ];
    }
}
