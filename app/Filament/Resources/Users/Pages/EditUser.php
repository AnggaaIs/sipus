<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = filled($data['name'] ?? null) ? $data['name'] : $data['full_name'];

        if (($data['account_status'] ?? null) === 'active' && blank($data['approved_at'] ?? null)) {
            $data['approved_at'] = now();
            $data['approved_by'] ??= Auth::id();
        }

        return $data;
    }
}
