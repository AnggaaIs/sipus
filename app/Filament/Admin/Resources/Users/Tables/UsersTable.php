<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Exports\UsersExport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label('Nama lengkap')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'Admin' : 'Siswa')
                    ->colors([
                        'warning' => 'admin',
                        'info' => 'user',
                    ]),
                TextColumn::make('account_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'pending' => 'Menunggu',
                        'rejected' => 'Ditolak',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'rejected',
                        'gray' => 'suspended',
                    ]),
                TextColumn::make('approved_at')
                    ->label('Disetujui pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('class')
                    ->label('Kelas')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->label('Peran')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'Siswa',
                    ]),
                SelectFilter::make('account_status')
                    ->label('Status akun')
                    ->options([
                        'pending' => 'Menunggu',
                        'active' => 'Aktif',
                        'rejected' => 'Ditolak',
                        'suspended' => 'Ditangguhkan',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->form([
                        Select::make('period')
                            ->label('Rentang Waktu')
                            ->options([
                                'all' => 'Semua',
                                '1_week' => '1 Minggu terakhir',
                                '1_month' => '1 Bulan terakhir',
                                '3_months' => '3 Bulan terakhir',
                                '6_months' => '6 Bulan terakhir',
                            ])
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $dates = match ($state) {
                                    '1_week' => [now()->subWeek()->toDateString(), now()->toDateString()],
                                    '1_month' => [now()->subMonth()->toDateString(), now()->toDateString()],
                                    '3_months' => [now()->subMonths(3)->toDateString(), now()->toDateString()],
                                    '6_months' => [now()->subMonths(6)->toDateString(), now()->toDateString()],
                                    default => [null, null],
                                };
                                $set('start_date', $dates[0]);
                                $set('end_date', $dates[1]);
                            }),
                        DatePicker::make('start_date')
                            ->label('Dari tanggal')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('end_date')
                            ->label('Sampai tanggal')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->action(fn (array $data) => UsersExport::xlsx(
                        $data['start_date'] ?? null,
                        $data['end_date'] ?? null,
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
