<?php

namespace App\Filament\Resources\Ddcs\Pages;

use App\Filament\Resources\Ddcs\DdcResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDdcs extends ListRecords
{
    protected static string $resource = DdcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
