<?php

namespace App\Filament\User\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class MyActiveLoansWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::whereBelongsTo(Auth::user())
                    ->whereIn('status', [Loan::STATUS_BORROWED, Loan::STATUS_OVERDUE])
                    ->with(['loanItems.book'])
                    ->latest('loan_date')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('loan_code')
                    ->label('Kode Pinjam')
                    ->fontFamily('mono')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('loanItems.book.title')
                    ->label('Judul Buku')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color(fn (Loan $record): string => $record->isOverdue() ? 'danger' : 'gray')
                    ->weight(fn (Loan $record): string => $record->isOverdue() ? 'bold' : 'normal'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Loan $record): string => $record->resolvedStatus())
                    ->color(fn (string $state): string => match ($state) {
                        Loan::STATUS_BORROWED => 'warning',
                        Loan::STATUS_OVERDUE => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Loan::statusLabel($state)),
            ])
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateHeading('Tidak ada peminjaman aktif')
            ->emptyStateDescription('Kamu belum meminjam buku saat ini.');
    }
}
