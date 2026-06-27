<?php

namespace App\Filament\User\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->description(fn (Loan $record): ?string => $record->isReturned()
                        ? 'Kondisi saat buku diterima kembali'
                        : 'Akan muncul setelah pengembalian diproses'),
                TextColumn::make('loan_date')
                    ->label('Tanggal pinjam')
                    ->dateTime()
                    ->sortable()
                    ->description('Tanggal transaksi dibuat'),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date()
                    ->sortable()
                    ->badge(fn (Loan $record): bool => ! $record->isReturned())
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
                    ->state(fn (Loan $record): string => $record->resolvedStatus())
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
                    ->options(Loan::statusOptions())
                    ->query(function (Builder $query, array $data): void {
                        match ($data['value'] ?? null) {
                            Loan::STATUS_BORROWED => $query->currentlyBorrowed(),
                            Loan::STATUS_RETURNED => $query->returned(),
                            Loan::STATUS_OVERDUE => $query->currentlyOverdue(),
                            default => null,
                        };
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateHeading('Belum ada riwayat peminjaman')
            ->emptyStateDescription('Riwayat buku yang kamu pinjam akan muncul di sini.');
    }

    public static function statusLabel(string $state): string
    {
        return Loan::statusLabel($state);
    }

    public static function statusColor(string $state): string
    {
        return match ($state) {
            Loan::STATUS_BORROWED => 'info',
            Loan::STATUS_RETURNED => 'success',
            Loan::STATUS_OVERDUE => 'danger',
            default => 'gray',
        };
    }

    public static function statusIcon(string $state): string
    {
        return match ($state) {
            Loan::STATUS_BORROWED => 'heroicon-m-book-open',
            Loan::STATUS_RETURNED => 'heroicon-m-check-circle',
            Loan::STATUS_OVERDUE => 'heroicon-m-exclamation-triangle',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    public static function conditionLabel(Loan $record): string
    {
        if (! $record->isReturned()) {
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
        if (! $record->isReturned()) {
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
        if (! $record->isReturned()) {
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
        if ($record->isReturned()) {
            return 'success';
        }

        return match (true) {
            $record->isOverdue() => 'danger',
            self::isDueSoon($record) => 'warning',
            default => 'gray',
        };
    }

    public static function dueDateDescription(Loan $record): ?string
    {
        if ($record->isReturned()) {
            return 'Pinjaman sudah selesai';
        }

        if ($record->isOverdue()) {
            return 'Segera kembalikan buku';
        }

        if (self::isDueSoon($record)) {
            return 'Jatuh tempo hampir tiba';
        }

        return 'Batas pengembalian aktif';
    }

    public static function dueDateIcon(Loan $record): ?string
    {
        if ($record->isReturned()) {
            return 'heroicon-m-check-circle';
        }

        return match (true) {
            $record->isOverdue() => 'heroicon-m-exclamation-triangle',
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

        if ($record->isOverdue()) {
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
        if ($record->isReturned() || $record->isOverdue()) {
            return false;
        }

        return now()->startOfDay()->diffInDays($record->due_date->copy()->startOfDay(), false) <= 2;
    }
}
