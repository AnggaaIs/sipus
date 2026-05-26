<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['loan_code'] = filled($data['loan_code'] ?? null)
            ? $data['loan_code']
            : 'PNJ-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->checkoutBooks();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
