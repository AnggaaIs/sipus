<?php

namespace App\Filament\Admin\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Actions\EditAction;
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
            ->toolbarActions([]);
    }
}
