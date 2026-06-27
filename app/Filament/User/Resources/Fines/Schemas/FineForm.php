<?php

namespace App\Filament\User\Resources\Fines\Schemas;

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
                    ->relationship('loan', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('overdue_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('amount_per_day')
                    ->required()
                    ->numeric()
                    ->default(1000.0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['unpaid' => 'Unpaid', 'paid' => 'Paid'])
                    ->default('unpaid')
                    ->required(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
