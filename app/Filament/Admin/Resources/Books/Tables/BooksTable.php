<?php

namespace App\Filament\Admin\Resources\Books\Tables;

use App\Exports\BooksExport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('authors.name')
                    ->label('Penulis')
                    ->badge()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->searchable(),
                TextColumn::make('ddc.code')
                    ->label('DDC')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('publisher.name')
                    ->label('Penerbit')
                    ->searchable(),
                TextColumn::make('publish_year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('total_copies')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_copies')
                    ->label('Tersedia')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('ddc_id')
                    ->label('DDC')
                    ->relationship('ddc', 'code')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
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
                    ->action(fn (array $data) => BooksExport::xlsx(
                        $data['start_date'] ?? null,
                        $data['end_date'] ?? null,
                    )),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
