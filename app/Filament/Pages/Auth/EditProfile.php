<?php

namespace App\Filament\Pages\Auth;

use App\Notifications\PasswordChangedNotification;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    public function save(): void
    {
        $previousPasswordHash = $this->getUser()->getAuthPassword();

        parent::save();

        $this->getUser()->refresh();

        if ($this->getUser()->getAuthPassword() !== $previousPasswordHash) {
            $this->getUser()->notify(new PasswordChangedNotification);
        }
    }

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
            $data['name'] = $this->getUser()->name;
            $data['full_name'] = $this->getUser()->full_name;
            $data['nisn'] = $this->getUser()->nisn;
            $data['class'] = $this->getUser()->class;
            $data['email'] = $this->getUser()->email;
            $data['phone'] = $this->getUser()->phone;
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
                ...$this->getIdentityFormComponents(),
                $this->getPasswordFormComponent()
                    ->label('Kata sandi')
                    ->rule(Password::defaults())
                    ->helperText('Kosongkan jika kata sandi tidak ingin diganti.'),
                $this->getPasswordConfirmationFormComponent()->label('Konfirmasi kata sandi'),
                $this->getCurrentPasswordFormComponent()->label('Kata sandi saat ini'),
            ]);
    }

    /**
     * @return array<int, Component>
     */
    protected function getIdentityFormComponents(): array
    {
        if ($this->getUser()->isMember()) {
            return [];
        }

        return [
            $this->getFullNameFormComponent(),
            $this->getNameFormComponent()
                ->label('Nama tampilan')
                ->helperText('Opsional. Jika dikosongkan, nama lengkap akan dipakai sebagai nama tampilan.'),
            $this->getNisnFormComponent(),
            $this->getClassFormComponent(),
            $this->getEmailFormComponent()->label('Email'),
            $this->getPhoneFormComponent(),
        ];
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

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->label('Kata sandi saat ini')
            ->belowContent(fn (): string => $this->getUser()->isMember()
                ? 'Wajib diisi untuk menyimpan perubahan profil apa pun. Jika ingin mengubah nama, email, NISN, kelas, atau nomor telepon, silakan hubungi admin.'
                : 'Wajib diisi untuk menyimpan perubahan profil apa pun.')
            ->required()
            ->visible(fn (Get $get): bool => true)
            ->dehydrated(false);
    }
}
