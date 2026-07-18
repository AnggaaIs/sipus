<?php

namespace App\Filament\Admin\Resources\Books\Schemas;

use App\Filament\Admin\Resources\Authors\Schemas\AuthorForm;
use App\Filament\Admin\Resources\Publishers\Schemas\PublisherForm;
use App\Models\Category;
use App\Models\Ddc;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('isbn')
                    ->label('ISBN')
                    ->helperText('Jika ISBN pernah dihapus, buka filter Terhapus di tabel buku lalu restore atau hapus permanen.')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'ISBN ini masih dipakai oleh buku aktif atau buku yang ada di arsip. Buka filter Terhapus lalu pulihkan, atau hapus permanen jika ingin membuat ulang.',
                    ]),
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Slug ini masih dipakai oleh buku aktif atau buku yang ada di arsip.',
                    ]),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('ddc_id')
                    ->label('DDC')
                    ->relationship('ddc', 'code')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Ddc $record): string => $record->code.' - '.$record->name)
                    ->default(null),
                Select::make('publisher_id')
                    ->label('Penerbit')
                    ->relationship('publisher', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        PublisherForm::nameField(),
                        PublisherForm::cityField(),
                    ])
                    ->default(null),
                Select::make('authors')
                    ->label('Penulis')
                    ->relationship('authors', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        AuthorForm::nameField(),
                        AuthorForm::bioField(),
                    ]),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('publish_year')
                    ->label('Tahun terbit')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue((int) now()->format('Y'))
                    ->default(null),
                TextInput::make('pages')
                    ->label('Jumlah halaman')
                    ->numeric()
                    ->minValue(1)
                    ->default(null),
                TextInput::make('language')
                    ->label('Bahasa')
                    ->required()
                    ->maxLength(5)
                    ->default('id'),
                FileUpload::make('cover')
                    ->label('Sampul Buku')
                    ->image()
                    ->disk('covers')
                    ->visibility('public')
                    ->directory(fn (Get $get) => Str::slug(Category::find($get('category_id'))?->name ?? 'uncategorized'))
                    ->getUploadedFileNameForStorageUsing(static fn (TemporaryUploadedFile $file): string => self::generateCoverFileName($file))
                    ->default(null),
                TextInput::make('total_copies')
                    ->label('Total Stok')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                TextInput::make('available_copies')
                    ->label('Stok Tersedia')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public static function generateCoverFileName(TemporaryUploadedFile $file): string
    {
        return Str::uuid()->toString().'.'.$file->guessExtension();
    }
}
