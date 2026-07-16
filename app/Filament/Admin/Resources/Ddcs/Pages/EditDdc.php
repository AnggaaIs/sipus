<?php

namespace App\Filament\Admin\Resources\Ddcs\Pages;

use App\Filament\Admin\Resources\Ddcs\DdcResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDdc extends EditRecord
{
    protected static string $resource = DdcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action): void {
                    if ($this->record->books()->withTrashed()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Data tidak dapat dihapus')
                            ->body('DDC ini masih digunakan oleh buku.')
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
