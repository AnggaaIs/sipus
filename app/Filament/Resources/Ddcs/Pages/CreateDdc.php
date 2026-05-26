<?php

namespace App\Filament\Resources\Ddcs\Pages;

use App\Filament\Resources\Ddcs\DdcResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDdc extends CreateRecord
{
    protected static string $resource = DdcResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
