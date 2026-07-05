<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingUsersWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::where('account_status', 'pending')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('class')
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->date('d M Y'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->url(fn (User $record): string => route('filament.admin.resources.users.edit', $record)),
            ])
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('Semua anggota sudah disetujui')
            ->emptyStateDescription('Tidak ada anggota yang menunggu persetujuan.');
    }

    public static function getSort(): int
    {
        return 2;
    }
}
