<?php

namespace App\Filament\User\Resources\Loans\Pages;

use App\Filament\User\Resources\Loans\LoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
}
