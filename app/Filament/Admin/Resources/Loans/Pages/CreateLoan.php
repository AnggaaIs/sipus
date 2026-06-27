<?php

namespace App\Filament\Admin\Resources\Loans\Pages;

use App\Filament\Admin\Resources\Loans\LoanResource;
use App\Models\Loan;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $loanDate = now();

        return [
            ...$data,
            'loan_date' => $loanDate,
            'due_date' => filled($data['due_date'] ?? null)
                ? $data['due_date']
                : $loanDate->copy()->addDays(7)->toDateString(),
            'returned_at' => null,
            'status' => Loan::STATUS_BORROWED,
        ];
    }

    protected function afterCreate(): void
    {
        /** @var Loan $record */
        $record = $this->getRecord();

        $record->checkoutBooks();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
