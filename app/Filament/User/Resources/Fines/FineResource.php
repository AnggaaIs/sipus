<?php

namespace App\Filament\User\Resources\Fines;

use App\Filament\User\Resources\Fines\Pages\ListFines;
use App\Filament\User\Resources\Fines\Schemas\FineForm;
use App\Filament\User\Resources\Fines\Tables\FinesTable;
use App\Models\Fine;
use App\Models\Loan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Perpustakaan Saya';

    protected static ?string $modelLabel = 'Denda Saya';

    protected static ?string $pluralModelLabel = 'Denda Saya';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return FineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFines::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        Loan::syncOverdueFines();

        return parent::getEloquentQuery()
            ->whereBelongsTo(Auth::user());
    }
}
