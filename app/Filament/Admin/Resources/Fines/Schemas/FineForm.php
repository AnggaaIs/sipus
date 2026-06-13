<?php

namespace App\Filament\Admin\Resources\Fines\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('loan_id')
                    ->label('Peminjaman')
                    ->relationship('loan', 'loan_code')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Siswa')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('overdue_days')
                    ->label('Hari terlambat')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('amount_per_day')
                    ->label('Denda per hari')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(1000.0),
                TextInput::make('total_amount')
                    ->label('Total denda')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options(['unpaid' => 'Belum dibayar', 'paid' => 'Lunas'])
                    ->default('unpaid')
                    ->required(),
                DateTimePicker::make('paid_at')
                    ->label('Dibayar pada'),
            ]);
    }
}
