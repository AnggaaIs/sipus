<?php

namespace App\Filament\Admin\Resources\Borrows\Pages;

use App\Filament\Admin\Resources\Borrows\BorrowResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
