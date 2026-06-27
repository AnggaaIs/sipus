<?php

namespace App\Filament\Admin\Resources\Pengembalians;

use App\Filament\Admin\Resources\Pengembalians\Pages\ListPengembalians;
use App\Filament\Admin\Resources\Pengembalians\Tables\PengembaliansTable;
use App\Models\Loan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PengembalianResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Sirkulasi';

    protected static ?string $navigationLabel = 'Pengembalian Buku';

    protected static ?string $modelLabel = 'Pengembalian';

    protected static ?string $pluralModelLabel = 'Pengembalian';

    protected static ?int $navigationSort = 2;

    /**
     * Override the base Eloquent query to only show active loans.
     *
     * @return Builder<Loan>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->active();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return PengembaliansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengembalians::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
