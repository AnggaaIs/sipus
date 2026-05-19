<?php

namespace App\Filament\Admin\Resources\Borrows\Pages;

use App\Filament\Admin\Resources\Borrows\BorrowResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
