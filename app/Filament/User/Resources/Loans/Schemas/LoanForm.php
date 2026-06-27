<?php

namespace App\Filament\User\Resources\Loans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('loan_code')
                    ->required(),
                DatePicker::make('loan_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                DateTimePicker::make('returned_at'),
                Select::make('status')
                    ->options(['borrowed' => 'Borrowed', 'returned' => 'Returned', 'overdue' => 'Overdue'])
                    ->default('borrowed')
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
