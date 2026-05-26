<?php

namespace App\Filament\Resources\Ddcs\Pages;

use App\Filament\Resources\Ddcs\DdcResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDdc extends EditRecord
{
    protected static string $resource = DdcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
