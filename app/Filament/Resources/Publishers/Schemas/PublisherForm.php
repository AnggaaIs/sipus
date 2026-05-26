<?php

namespace App\Filament\Resources\Publishers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PublisherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::nameField(),
                self::cityField(),
            ]);
    }

    public static function nameField(): TextInput
    {
        return TextInput::make('name')
            ->label('Nama')
            ->required()
            ->maxLength(255);
    }

    public static function cityField(): TextInput
    {
        return TextInput::make('city')
            ->label('Kota')
            ->maxLength(255)
            ->default(null);
    }
}
