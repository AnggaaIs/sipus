<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('returnBooks')
                ->label('Kembalikan')
                ->color('success')
                ->icon('heroicon-o-arrow-uturn-left')
                ->visible(fn (): bool => $this->record->status !== 'returned')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->returnBooks();

                    Notification::make()
                        ->success()
                        ->title('Buku berhasil dikembalikan')
                        ->send();
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->record->status === 'returned'),
        ];
    }
}
