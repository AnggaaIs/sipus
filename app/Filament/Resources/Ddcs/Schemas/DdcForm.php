<?php

namespace App\Filament\Resources\Ddcs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DdcForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode DDC')
                    ->required()
                    ->minLength(3)
                    ->maxLength(3)
                    ->regex('/^\d{3}$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Gunakan 3 digit, contoh: 500 atau 813.'),
                TextInput::make('name')
                    ->label('Nama Klasifikasi')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
