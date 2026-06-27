<?php

namespace App\Filament\User\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label('Cover')
                    ->state(fn (Loan $record): ?string => $record->loanItems->first()?->book?->cover_url)
                    ->defaultImageUrl(asset('images/sepang_sma_logo.png'))
                    ->square()
                    ->imageHeight(56)
                    ->extraImgAttributes([
                        'alt' => 'Cover buku',
                        'loading' => 'lazy',
                        'class' => 'rounded-lg object-cover ring-1 ring-gray-200',
                    ], merge: true),
                TextColumn::make('loan_code')
                    ->label('Kode Peminjaman')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('medium')
                    ->description(fn (Loan $record): string => self::loanCodeDescription($record)),
                TextColumn::make('loanItems.book.title')
                    ->label('Buku')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->weight('medium')
                    ->description(fn (Loan $record): ?string => self::bookSummaryDescription($record)),
                TextColumn::make('loanItems')
                    ->label('Kondisi Buku')
                    ->badge()
                    ->state(fn (Loan $record): string => self::conditionLabel($record))
                    ->color(fn (Loan $record): string => self::conditionColor($record))
                    ->icon(fn (Loan $record): string => self::conditionIcon($record))
                    ->description(fn (Loan $record): ?string => $record->status === 'returned'
                        ? 'Kondisi saat buku diterima kembali'
                        : 'Akan muncul setelah pengembalian diproses'),
                TextColumn::make('loan_date')
                    ->label('Tanggal pinjam')
                    ->date()
                    ->sortable()
                    ->description('Tanggal transaksi dibuat'),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date()
                    ->sortable()
                    ->badge(fn (Loan $record): bool => $record->status !== 'returned')
                    ->color(fn (Loan $record): string => self::dueDateColor($record))
                    ->icon(fn (Loan $record): ?string => self::dueDateIcon($record))
                    ->description(fn (Loan $record): ?string => self::dueDateDescription($record)),
                TextColumn::make('returned_at')
                    ->label('Dikembalikan')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Belum dikembalikan'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                    ->icon(fn (string $state): string => self::statusIcon($state))
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('loan_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateHeading('Belum ada riwayat peminjaman')
            ->emptyStateDescription('Riwayat buku yang kamu pinjam akan muncul di sini.');
    }

    public static function statusLabel(string $state): string
    {
        return match ($state) {
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            default => $state,
        };
    }

    public static function statusColor(string $state): string
    {
        return match ($state) {
            'borrowed' => 'info',
            'returned' => 'success',
            'overdue' => 'danger',
            default => 'gray',
        };
    }

    public static function statusIcon(string $state): string
    {
        return match ($state) {
            'borrowed' => 'heroicon-m-book-open',
            'returned' => 'heroicon-m-check-circle',
            'overdue' => 'heroicon-m-exclamation-triangle',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    public static function conditionLabel(Loan $record): string
    {
        if ($record->status !== 'returned') {
            return 'Menunggu pengembalian';
        }

        $conditions = $record->loanItems
            ->map(fn ($loanItem): string => match ($loanItem->condition_on_return) {
                'damaged' => 'Rusak',
                'lost' => 'Hilang',
                default => 'Baik',
            })
            ->unique()
            ->values()
            ->all();

        return implode(', ', $conditions);
    }

    public static function conditionColor(Loan $record): string
    {
        if ($record->status !== 'returned') {
            return 'gray';
        }

        $conditions = $record->loanItems->pluck('condition_on_return');

        return match (true) {
            $conditions->contains('lost') => 'danger',
            $conditions->contains('damaged') => 'warning',
            default => 'success',
        };
    }

    public static function conditionIcon(Loan $record): string
    {
        if ($record->status !== 'returned') {
            return 'heroicon-m-arrow-path';
        }

        $conditions = $record->loanItems->pluck('condition_on_return');

        return match (true) {
            $conditions->contains('lost') => 'heroicon-m-x-circle',
            $conditions->contains('damaged') => 'heroicon-m-exclamation-triangle',
            default => 'heroicon-m-check-badge',
        };
    }

    public static function dueDateColor(Loan $record): string
    {
        if ($record->status === 'returned') {
            return 'success';
        }

        return match (true) {
            $record->due_date->isPast() => 'danger',
            self::isDueSoon($record) => 'warning',
            default => 'gray',
        };
    }

    public static function dueDateDescription(Loan $record): ?string
    {
        if ($record->status === 'returned') {
            return 'Pinjaman sudah selesai';
        }

        if ($record->due_date->isPast()) {
            return 'Segera kembalikan buku';
        }

        if (self::isDueSoon($record)) {
            return 'Jatuh tempo hampir tiba';
        }

        return 'Batas pengembalian aktif';
    }

    public static function dueDateIcon(Loan $record): ?string
    {
        if ($record->status === 'returned') {
            return 'heroicon-m-check-circle';
        }

        return match (true) {
            $record->due_date->isPast() => 'heroicon-m-exclamation-triangle',
            self::isDueSoon($record) => 'heroicon-m-clock',
            default => 'heroicon-m-calendar-days',
        };
    }

    public static function loanCodeDescription(Loan $record): string
    {
        $count = $record->loanItems->count();

        if (self::isDueSoon($record)) {
            return $count.' buku • Segera jatuh tempo';
        }

        if ($record->due_date->isPast() && $record->status !== 'returned') {
            return $count.' buku • Terlambat dikembalikan';
        }

        return $count.' buku';
    }

    public static function bookSummaryDescription(Loan $record): ?string
    {
        $firstBook = $record->loanItems->first()?->book;

        if ($firstBook === null) {
            return null;
        }

        return collect([
            $firstBook->publisher?->name,
            $firstBook->publish_year,
        ])->filter()->implode(' • ');
    }

    public static function isDueSoon(Loan $record): bool
    {
        if ($record->status === 'returned' || $record->due_date->isPast()) {
            return false;
        }

        return now()->startOfDay()->diffInDays($record->due_date->copy()->startOfDay(), false) <= 2;
    }
}
