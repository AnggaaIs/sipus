<?php

namespace App\Filament\Admin\Resources\Publishers\Pages;

use App\Filament\Admin\Resources\Publishers\PublisherResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPublisher extends EditRecord
{
    protected static string $resource = PublisherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
