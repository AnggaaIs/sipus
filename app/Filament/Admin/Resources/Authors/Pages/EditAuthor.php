<?php

namespace App\Filament\Admin\Resources\Authors\Pages;

use App\Filament\Admin\Resources\Authors\AuthorResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAuthor extends EditRecord
{
    protected static string $resource = AuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action): void {
                    if ($this->record->books()->withTrashed()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Data tidak dapat dihapus')
                            ->body('Penulis ini masih digunakan oleh buku.')
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
