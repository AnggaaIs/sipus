<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeApprovalData(array $data, ?User $record = null): array
    {
        $status = $data['account_status'] ?? $record?->account_status ?? 'pending';

        if ($status === 'active') {
            $data['rejection_reason'] = null;

            $shouldStampApproval = $record === null
                || $record->account_status !== 'active'
                || blank($record->approved_at)
                || blank($record->approved_by);

            if ($shouldStampApproval) {
                $data['approved_at'] = now();
                $data['approved_by'] = Auth::id();
            } else {
                $data['approved_at'] ??= $record->approved_at;
                $data['approved_by'] ??= $record->approved_by;
            }

            return $data;
        }

        if ($status === 'rejected') {
            $data['approved_at'] = null;
            $data['approved_by'] = null;

            return $data;
        }

        $data['rejection_reason'] = null;

        if ($status === 'pending') {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        } elseif ($record !== null) {
            $data['approved_at'] ??= $record->approved_at;
            $data['approved_by'] ??= $record->approved_by;
        } else {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        }

        return $data;
    }
}
