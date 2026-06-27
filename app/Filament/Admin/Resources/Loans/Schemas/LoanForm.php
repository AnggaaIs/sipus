<?php

namespace App\Filament\Admin\Resources\Loans\Schemas;

use App\Models\Loan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Siswa')
                    ->relationship(
                        'user',
                        'full_name',
                        fn (Builder $query): Builder => $query
                            ->where('role', 'user')
                            ->where('account_status', 'active')
                            ->where('is_active', true),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('loan_code')
                    ->label('Kode peminjaman')
                    ->default(fn (): string => 'SIPUS-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Placeholder::make('loan_date_preview')
                    ->label('Tanggal pinjam')
                    ->content(fn (?Loan $record): string => ($record?->loan_date ?? now())->format('d/m/Y H:i:s')),
                DatePicker::make('due_date')
                    ->label('Jatuh tempo')
                    ->default(now()->addDays(7))
                    ->required(),
                Placeholder::make('returned_at_preview')
                    ->label('Returned at')
                    ->content(fn (?Loan $record): string => $record?->returned_at?->format('d/m/Y H:i:s') ?? 'Belum dikembalikan')
                    ->hiddenOn('create'),
                Placeholder::make('status_preview')
                    ->label('Status')
                    ->content(fn (?Loan $record): string => Loan::statusLabel($record?->resolvedStatus() ?? Loan::STATUS_BORROWED)),
                Repeater::make('loanItems')
                    ->label('Daftar buku')
                    ->relationship()
                    ->schema([
                        Select::make('book_id')
                            ->label('Buku')
                            ->relationship(
                                'book',
                                'title',
                                fn (Builder $query): Builder => $query->where('available_copies', '>', 0),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->addActionLabel('Tambah buku')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
