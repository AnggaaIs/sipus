<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nisn')
                    ->label('NISN')
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->default(null),
                TextInput::make('full_name')
                    ->label('Nama lengkap')
                    ->required()
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('name')
                    ->label('Nama tampilan')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('role')
                    ->label('Peran')
                    ->options(['admin' => 'Admin', 'user' => 'Siswa'])
                    ->default('user')
                    ->required(),
                Select::make('account_status')
                    ->label('Status akun')
                    ->options([
                        'pending' => 'Menunggu',
                        'active' => 'Aktif',
                        'rejected' => 'Ditolak',
                        'suspended' => 'Ditangguhkan',
                    ])
                    ->default('pending')
                    ->required(),
                Textarea::make('rejection_reason')
                    ->label('Alasan penolakan')
                    ->rows(3)
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('approved_at')
                    ->label('Disetujui pada'),
                Select::make('approved_by')
                    ->label('Disetujui oleh')
                    ->relationship('approvedBy', 'full_name')
                    ->searchable()
                    ->preload()
                    ->default(null),
                TextInput::make('password')
                    ->label('Kata sandi')
                    ->password()
                    ->revealable()
                    ->rule(Password::defaults())
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Kosongkan jika kata sandi tidak ingin diganti.'),
                TextInput::make('class')
                    ->label('Kelas')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('phone')
                    ->label('Nomor telepon')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('avatar')
                    ->label('Foto profil')
                    ->maxLength(255)
                    ->default(null),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
