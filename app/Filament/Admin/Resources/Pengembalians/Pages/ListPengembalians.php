<?php

namespace App\Filament\Admin\Resources\Pengembalians\Pages;

use App\Filament\Admin\Resources\Pengembalians\PengembalianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengembalians extends ListRecords
{
    protected static string $resource = PengembalianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
