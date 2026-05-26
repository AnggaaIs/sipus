<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['name'] ??= $data['full_name'] ?? null;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->getUser()->isMember()) {
            $data['full_name'] = $this->getUser()->full_name;
            $data['nisn'] = $this->getUser()->nisn;
            $data['class'] = $this->getUser()->class;
        }

        $fullName = array_key_exists('full_name', $data)
            ? trim((string) $data['full_name'])
            : $this->getUser()->full_name;

        $data['full_name'] = $fullName;
        $data['name'] = filled($data['name'] ?? null) ? trim((string) $data['name']) : $fullName;
        $data['email'] = Str::lower(trim((string) ($data['email'] ?? '')));
        $data['phone'] = filled($data['phone'] ?? null) ? trim((string) $data['phone']) : null;
        $data['class'] = array_key_exists('class', $data)
            ? (filled($data['class'] ?? null) ? trim((string) $data['class']) : null)
            : $this->getUser()->class;
        $data['nisn'] = array_key_exists('nisn', $data)
            ? (filled($data['nisn'] ?? null) ? preg_replace('/\D+/', '', (string) $data['nisn']) : null)
            : $this->getUser()->nisn;

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFullNameFormComponent(),
                $this->getNameFormComponent()
                    ->label('Nama tampilan')
                    ->helperText('Opsional. Jika dikosongkan, nama lengkap akan dipakai sebagai nama tampilan.'),
                $this->getNisnFormComponent(),
                $this->getClassFormComponent(),
                $this->getEmailFormComponent()->label('Email'),
                $this->getPhoneFormComponent(),
                $this->getAvatarFormComponent(),
                $this->getPasswordFormComponent()
                    ->label('Kata sandi')
                    ->rule(Password::defaults())
                    ->helperText('Kosongkan jika kata sandi tidak ingin diganti.'),
                $this->getPasswordConfirmationFormComponent()->label('Konfirmasi kata sandi'),
                $this->getCurrentPasswordFormComponent()->label('Kata sandi saat ini'),
            ]);
    }

    protected function getFullNameFormComponent(): Component
    {
        return TextInput::make('full_name')
            ->label('Nama lengkap')
            ->required()
            ->maxLength(255)
            ->autofocus()
            ->disabled(fn (): bool => $this->getUser()->isMember())
            ->dehydrated(fn (): bool => ! $this->getUser()->isMember())
            ->helperText(fn (): ?string => $this->getUser()->isMember()
                ? 'Nama lengkap hanya dapat diubah oleh admin.'
                : null);
    }

    protected function getNisnFormComponent(): Component
    {
        return TextInput::make('nisn')
            ->label('NISN')
            ->maxLength(10)
            ->rule('digits:10')
            ->unique(ignoreRecord: true)
            ->disabled()
            ->dehydrated(false)
            ->helperText('NISN hanya dapat diubah oleh admin.')
            ->visible(fn (): bool => $this->getUser()->isMember());
    }

    protected function getClassFormComponent(): Component
    {
        return TextInput::make('class')
            ->label('Kelas')
            ->maxLength(100)
            ->disabled()
            ->dehydrated(false)
            ->helperText('Kelas hanya dapat diubah oleh admin.')
            ->visible(fn (): bool => $this->getUser()->isMember());
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Nomor telepon')
            ->tel()
            ->maxLength(30);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('Foto profil')
            ->disk('public')
            ->directory('avatars')
            ->avatar()
            ->imageEditor()
            ->circleCropper()
            ->maxSize(2048)
            ->preventFilePathTampering()
            ->helperText('Unggah foto JPG, PNG, atau WebP maksimal 2 MB.');
    }
}
