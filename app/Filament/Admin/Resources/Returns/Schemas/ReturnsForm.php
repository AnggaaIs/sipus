<?php

namespace App\Filament\Admin\Resources\Returns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReturnsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('borrow_id')
                    ->required()
                    ->numeric(),
                TextInput::make('petugas_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('return_date')
                    ->required(),
                Select::make('condition')
                    ->options(['baik' => 'Baik', 'rusak' => 'Rusak', 'hilang' => 'Hilang'])
                    ->default('baik')
                    ->required(),
            ]);
    }
}
