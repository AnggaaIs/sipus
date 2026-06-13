<?php

namespace App\Filament\Admin\Resources\Loans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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
                        fn(Builder $query): Builder => $query
                            ->where('role', 'user')
                            ->where('account_status', 'active')
                            ->where('is_active', true),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('loan_code')
                    ->label('Kode peminjaman')
                    ->default(fn(): string => 'SIPUS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                DatePicker::make('loan_date')
                    ->label('Tanggal pinjam')
                    ->default(now())
                    ->required(),
                DatePicker::make('due_date')
                    ->label('Jatuh tempo')
                    ->default(now()->addDays(7))
                    ->required(),
                DateTimePicker::make('returned_at'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                    ])
                    ->default('borrowed')
                    ->required(),
                Repeater::make('loanItems')
                    ->label('Daftar buku')
                    ->relationship()
                    ->schema([
                        Select::make('book_id')
                            ->label('Buku')
                            ->relationship(
                                'book',
                                'title',
                                fn(Builder $query): Builder => $query->where('available_copies', '>', 0),
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
