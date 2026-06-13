<?php

namespace App\Filament\Admin\Resources\Pengembalians\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengembaliansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan_code')
                    ->label('Kode Pinjam')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('loanItems.book.title')
                    ->label('Buku')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
                TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable()
                    ->color(fn(Loan $record): string => $record->due_date->isPast() ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'borrowed' => 'Dipinjam',
                        'overdue' => 'Terlambat',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'info' => 'borrowed',
                        'danger' => 'overdue',
                    ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'borrowed' => 'Dipinjam',
                        'overdue' => 'Terlambat',
                    ]),
            ])
            ->recordActions([
                Action::make('returnBooks')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->schema([
                        Select::make('condition_on_return')
                            ->label('Kondisi buku saat dikembalikan')
                            ->options([
                                'good' => '✅ Baik',
                                'damaged' => '⚠️ Rusak',
                                'lost' => '❌ Hilang',
                            ])
                            ->default('good')
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Proses Pengembalian Buku')
                    ->modalDescription(fn(Loan $record): string => "Konfirmasi pengembalian untuk kode: {$record->loan_code} atas nama {$record->user->name}")
                    ->modalSubmitActionLabel('Proses Pengembalian')
                    ->action(function (Loan $record, array $data): void {
                        $record->returnBooks($data['condition_on_return']);

                        $record->refresh()->load('fine');
                        $fine = $record->fine;
                        $hasFine = $fine && $fine->status === 'unpaid';

                        if ($hasFine) {
                            $total = number_format($fine->total_amount, 0, ',', '.');
                            Notification::make()
                                ->warning()
                                ->title('Buku Dikembalikan — Ada Denda!')
                                ->body("Terlambat {$fine->overdue_days} hari. Denda: Rp {$total}")
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title('Buku Berhasil Dikembalikan')
                                ->body("Peminjaman {$record->loan_code} selesai tanpa denda.")
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([])
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('Tidak Ada Buku yang Dipinjam')
            ->emptyStateDescription('Semua buku sudah dikembalikan. Tidak ada peminjaman aktif saat ini.');
    }
}
