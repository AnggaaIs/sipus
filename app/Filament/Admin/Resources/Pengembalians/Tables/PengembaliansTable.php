<?php

namespace App\Filament\Admin\Resources\Pengembalians\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

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
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable()
                    ->color(fn (Loan $record): string => $record->due_date->isPast() ? 'danger' : 'success'),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Loan $record): string => $record->resolvedStatus())
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Loan::statusLabel($state))
                    ->colors([
                        'info' => Loan::STATUS_BORROWED,
                        'danger' => Loan::STATUS_OVERDUE,
                    ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Loan::STATUS_BORROWED => Loan::statusLabel(Loan::STATUS_BORROWED),
                        Loan::STATUS_OVERDUE => Loan::statusLabel(Loan::STATUS_OVERDUE),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        match ($data['value'] ?? null) {
                            Loan::STATUS_BORROWED => $query->currentlyBorrowed(),
                            Loan::STATUS_OVERDUE => $query->currentlyOverdue(),
                            default => null,
                        };
                    }),
            ])
            ->recordActions([
                Action::make('returnBooks')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->fillForm(function (Loan $record): array {
                        $record->syncFine();
                        $record->refresh()->load('fine');

                        return [
                            'settle_fine' => false,
                        ];
                    })
                    ->schema(function (Loan $record): array {
                        $record->refresh()->load('fine');
                        $fine = $record->fine;
                        $hasOutstandingFine = $record->hasOutstandingFine();
                        $formattedTotal = $fine !== null
                            ? number_format((float) $fine->total_amount, 0, ',', '.')
                            : '0';

                        return [
                            Select::make('condition_on_return')
                                ->label('Kondisi buku saat dikembalikan')
                                ->options([
                                    'good' => '✅ Baik',
                                    'damaged' => '⚠️ Rusak',
                                    'lost' => '❌ Hilang',
                                ])
                                ->default('good')
                                ->required(),
                            Placeholder::make('fine_summary')
                                ->label('Denda keterlambatan')
                                ->content($hasOutstandingFine
                                    ? "Terlambat {$fine?->overdue_days} hari. Total denda saat ini: Rp {$formattedTotal}."
                                    : 'Tidak ada denda yang perlu dilunasi untuk memproses pengembalian ini.')
                                ->visible($hasOutstandingFine),
                            Checkbox::make('settle_fine')
                                ->label($hasOutstandingFine
                                    ? "Lunasi denda Rp {$formattedTotal} dan lanjutkan pengembalian"
                                    : 'Lanjutkan pengembalian')
                                ->helperText('Centang untuk menandai denda lunas langsung dari modal pengembalian ini.')
                                ->visible($hasOutstandingFine),
                        ];
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Proses Pengembalian Buku')
                    ->modalDescription(fn (Loan $record): string => "Konfirmasi pengembalian untuk kode: {$record->loan_code} atas nama {$record->user->name}")
                    ->modalSubmitActionLabel(function (Loan $record): string {
                        $record->syncFine();
                        $record->refresh()->load('fine');

                        return $record->hasOutstandingFine()
                            ? 'Lunasi Denda & Proses Pengembalian'
                            : 'Proses Pengembalian';
                    })
                    ->action(function (Loan $record, array $data): void {
                        $record->syncFine();
                        $record->refresh()->load('fine');

                        if ($record->hasOutstandingFine() && ! ($data['settle_fine'] ?? false)) {
                            $total = number_format((float) $record->fine?->total_amount, 0, ',', '.');

                            Notification::make()
                                ->danger()
                                ->title('Pengembalian ditolak')
                                ->body("Centang pelunasan denda terlebih dahulu. Total saat ini: Rp {$total}")
                                ->send();

                            return;
                        }

                        if ($record->hasOutstandingFine()) {
                            $record->settleFine();
                            $record->refresh()->load('fine');
                        }

                        try {
                            $record->returnBooks($data['condition_on_return']);
                        } catch (ValidationException) {
                            $record->refresh()->load('fine');
                            $fine = $record->fine;
                            $total = $fine !== null ? number_format((float) $fine->total_amount, 0, ',', '.') : '0';

                            Notification::make()
                                ->danger()
                                ->title('Pengembalian ditolak')
                                ->body("Denda keterlambatan harus dilunasi terlebih dahulu. Total saat ini: Rp {$total}")
                                ->send();

                            return;
                        }

                        $record->refresh()->load('fine');
                        $fine = $record->fine;
                        $wasFineSettled = $fine !== null && $fine->status === 'paid';

                        Notification::make()
                            ->success()
                            ->title('Buku Berhasil Dikembalikan')
                            ->body($wasFineSettled
                                ? "Peminjaman {$record->loan_code} selesai dan denda sudah dilunasi."
                                : "Peminjaman {$record->loan_code} selesai tanpa denda.")
                            ->send();
                    }),
            ])
            ->toolbarActions([])
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('Tidak Ada Buku yang Dipinjam')
            ->emptyStateDescription('Semua buku sudah dikembalikan. Tidak ada peminjaman aktif saat ini.');
    }
}
