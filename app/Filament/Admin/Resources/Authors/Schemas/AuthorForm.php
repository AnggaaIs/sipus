<?php

namespace App\Filament\Admin\Resources\Authors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::nameField(),
                self::bioField(),
            ]);
    }

    public static function nameField(): TextInput
    {
        return TextInput::make('name')
            ->label('Nama')
            ->required()
            ->maxLength(255);
    }

    public static function bioField(): Textarea
    {
        return Textarea::make('bio')
            ->label('Bio')
            ->rows(4)
            ->default(null)
            ->columnSpanFull();
    }
}
