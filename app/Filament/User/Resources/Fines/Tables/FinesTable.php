<?php

namespace App\Filament\User\Resources\Fines\Tables;

use Filament\Tables\Columns\TextColumn;
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
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
