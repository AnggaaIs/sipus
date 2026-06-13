<?php

namespace App\Filament\Admin\Resources\Fines\Tables;

use App\Models\Fine;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                    ->formatStateUsing(fn(string $state): string => $state === 'paid' ? 'Lunas' : 'Belum dibayar')
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
                    ->visible(fn(Fine $record): bool => $record->status === 'unpaid')
                    ->requiresConfirmation()
                    ->action(function (Fine $record): void {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Denda ditandai lunas')
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
