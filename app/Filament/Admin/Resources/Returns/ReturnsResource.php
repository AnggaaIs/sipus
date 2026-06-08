<?php

namespace App\Filament\Admin\Resources\Returns;

use App\Filament\Admin\Resources\Returns\Pages\CreateReturns;
use App\Filament\Admin\Resources\Returns\Pages\EditReturns;
use App\Filament\Admin\Resources\Returns\Pages\ListReturns;
use App\Filament\Admin\Resources\Returns\Schemas\ReturnsForm;
use App\Filament\Admin\Resources\Returns\Tables\ReturnsTable;
use App\Models\Returns;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReturnsResource extends Resource
{
    protected static ?string $model = Returns::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReturnsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnsTable::configure($table);
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
            'index' => ListReturns::route('/'),
            'create' => CreateReturns::route('/create'),
            'edit' => EditReturns::route('/{record}/edit'),
        ];
    }
}
