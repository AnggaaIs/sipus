<?php

namespace App\Filament\User\Resources\Fines\Pages;

use App\Filament\User\Resources\Fines\FineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFine extends CreateRecord
{
    protected static string $resource = FineResource::class;
}
