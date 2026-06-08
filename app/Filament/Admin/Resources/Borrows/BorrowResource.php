<?php

namespace App\Filament\Admin\Resources\Borrows;

use App\Filament\Admin\Resources\Borrows\Pages\CreateBorrow;
use App\Filament\Admin\Resources\Borrows\Pages\EditBorrow;
use App\Filament\Admin\Resources\Borrows\Pages\ListBorrows;
use App\Filament\Admin\Resources\Borrows\Schemas\BorrowForm;
use App\Filament\Admin\Resources\Borrows\Tables\BorrowsTable;
use App\Models\Borrow;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BorrowForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BorrowsTable::configure($table);
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
            'index' => ListBorrows::route('/'),
            'create' => CreateBorrow::route('/create'),
            'edit' => EditBorrow::route('/{record}/edit'),
        ];
    }
}
