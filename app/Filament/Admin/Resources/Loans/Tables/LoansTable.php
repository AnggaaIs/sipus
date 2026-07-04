<?php

namespace App\Filament\Admin\Resources\Loans\Tables;

use App\Exports\LoansExport;
use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
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
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable(),
                TextColumn::make('loan_code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('loanItems.book.title')
                    ->label('Buku')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
                TextColumn::make('loan_date')
                    ->label('Tanggal pinjam')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->label('Dikembalikan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Loan $record): string => $record->resolvedStatus())
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Loan::statusLabel($state))
                    ->colors([
                        'info' => Loan::STATUS_BORROWED,
                        'success' => Loan::STATUS_RETURNED,
                        'danger' => Loan::STATUS_OVERDUE,
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Select::make('period')
                            ->label('Rentang Waktu')
                            ->options([
                                'all' => 'Semua',
                                '1_week' => '1 Minggu terakhir',
                                '1_month' => '1 Bulan terakhir',
                                '3_months' => '3 Bulan terakhir',
                                '6_months' => '6 Bulan terakhir',
                            ])
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $dates = match ($state) {
                                    '1_week' => [now()->subWeek()->toDateString(), now()->toDateString()],
                                    '1_month' => [now()->subMonth()->toDateString(), now()->toDateString()],
                                    '3_months' => [now()->subMonths(3)->toDateString(), now()->toDateString()],
                                    '6_months' => [now()->subMonths(6)->toDateString(), now()->toDateString()],
                                    default => [null, null],
                                };
                                $set('start_date', $dates[0]);
                                $set('end_date', $dates[1]);
                            }),
                        DatePicker::make('start_date')
                            ->label('Dari tanggal')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('end_date')
                            ->label('Sampai tanggal')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->action(fn (array $data) => LoansExport::xlsx(
                        $data['start_date'] ?? null,
                        $data['end_date'] ?? null,
                    )),
            ]);
    }
}
