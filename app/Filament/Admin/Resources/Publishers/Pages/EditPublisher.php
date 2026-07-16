<?php

namespace App\Filament\Admin\Resources\Publishers\Pages;

use App\Filament\Admin\Resources\Publishers\PublisherResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPublisher extends EditRecord
{
    protected static string $resource = PublisherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action): void {
                    if ($this->record->books()->withTrashed()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Data tidak dapat dihapus')
                            ->body('Penerbit ini masih digunakan oleh buku.')
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
