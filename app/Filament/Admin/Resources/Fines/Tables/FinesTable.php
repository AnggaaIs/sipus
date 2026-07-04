<?php

namespace App\Filament\Admin\Resources\Fines\Tables;

use App\Exports\FinesExport;
use App\Models\Fine;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan.loan_code')
                    ->label('Kode pinjam')
                    ->searchable(),
                TextColumn::make('user.full_name')
                    ->label('Siswa')
                    ->searchable(),
                TextColumn::make('overdue_days')
                    ->label('Hari')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount_per_day')
                    ->label('Per hari')
                    ->money('IDR')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'paid' ? 'Lunas' : 'Belum dibayar')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'unpaid',
                    ]),
                TextColumn::make('paid_at')
                    ->label('Dibayar pada')
                    ->dateTime()
                    ->sortable(),
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
                    ->options([
                        'unpaid' => 'Belum dibayar',
                        'paid' => 'Lunas',
                    ]),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Tandai lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Fine $record): bool => $record->status === 'unpaid')
                    ->requiresConfirmation()
                    ->action(function (Fine $record): void {
                        $record->loan?->syncFine();
                        $record->refresh();

                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Denda ditandai lunas')
                            ->send();
                    }),
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
                    ->action(fn (array $data) => FinesExport::xlsx(
                        $data['start_date'] ?? null,
                        $data['end_date'] ?? null,
                    )),
            ]);
    }
}
