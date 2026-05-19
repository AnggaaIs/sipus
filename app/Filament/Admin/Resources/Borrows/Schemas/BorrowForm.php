<?php

namespace App\Filament\Admin\Resources\Borrows\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BorrowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('users_id')
                    ->required()
                    ->numeric(),
                TextInput::make('books_id')
                    ->required()
                    ->numeric(),
                TextInput::make('petugas_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('borrow_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                Select::make('status')
                    ->options(['dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan'])
                    ->default('dipinjam')
                    ->required(),
            ]);
    }
}
