<?php

namespace App\Filament\User\Resources\Fines\Pages;

use App\Filament\User\Resources\Fines\FineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFines extends ListRecords
{
    protected static string $resource = FineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
