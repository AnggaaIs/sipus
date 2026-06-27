<?php

namespace App\Filament\User\Resources\Loans;

use App\Filament\User\Resources\Loans\Pages\ListLoans;
use App\Filament\User\Resources\Loans\Schemas\LoanForm;
use App\Filament\User\Resources\Loans\Tables\LoansTable;
use App\Models\Loan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Perpustakaan Saya';

    protected static ?string $modelLabel = 'Peminjaman Saya';

    protected static ?string $pluralModelLabel = 'Peminjaman Saya';

    protected static ?string $recordTitleAttribute = 'loan_code';

    public static function form(Schema $schema): Schema
    {
        return LoanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoansTable::configure($table);
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
            'index' => ListLoans::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'loanItems.book.publisher',
            ])
            ->whereBelongsTo(Auth::user());
    }
}
