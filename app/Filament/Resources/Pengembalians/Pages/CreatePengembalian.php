<?php

namespace App\Filament\Resources\Pengembalians\Pages;

use App\Filament\Resources\Pengembalians\PengembalianResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengembalian extends CreateRecord
{
    protected static string $resource = PengembalianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
