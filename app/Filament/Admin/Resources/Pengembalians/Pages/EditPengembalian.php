<?php

namespace App\Filament\Admin\Resources\Pengembalians\Pages;

use App\Filament\Admin\Resources\Pengembalians\PengembalianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengembalian extends EditRecord
{
    protected static string $resource = PengembalianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
