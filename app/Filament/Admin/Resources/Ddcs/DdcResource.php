<?php

namespace App\Filament\Admin\Resources\Ddcs;

use App\Filament\Admin\Resources\Ddcs\Pages\CreateDdc;
use App\Filament\Admin\Resources\Ddcs\Pages\EditDdc;
use App\Filament\Admin\Resources\Ddcs\Pages\ListDdcs;
use App\Filament\Admin\Resources\Ddcs\Schemas\DdcForm;
use App\Filament\Admin\Resources\Ddcs\Tables\DdcsTable;
use App\Models\Ddc;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DdcResource extends Resource
{
    protected static ?string $model = Ddc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'DDC';

    protected static ?string $pluralModelLabel = 'DDC';

    protected static ?string $recordTitleAttribute = 'code';


    public static function form(Schema $schema): Schema
    {
        return DdcForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DdcsTable::configure($table);
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
            'index' => ListDdcs::route('/'),
            'create' => CreateDdc::route('/create'),
            'edit' => EditDdc::route('/{record}/edit'),
        ];
    }
}
