<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Filament\Resources\Authors\Schemas\AuthorForm;
use App\Filament\Resources\Publishers\Schemas\PublisherForm;
use App\Models\Author;
use App\Models\Category;
use App\Models\Ddc;
use App\Models\Publisher;
use App\Services\Books\BookMetadataLookup;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                    ])
                    ->suffixAction(
                        Action::make('fillFromIsbn')
                            ->icon('heroicon-m-sparkles')
                            ->color('primary')
                            ->tooltip('Isi otomatis dari ISBN')
                            ->action(fn (Get $get, Set $set) => self::fillFromIsbn($get, $set)),
                    ),
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
                    ->directory(fn (Get $get) => Str::slug(Category::find($get('category_id'))?->name ?? 'uncategorized'))
                    ->getUploadedFileNameForStorageUsing(fn (Get $get, $file) => Str::slug($get('title') ?? 'cover').'.'.$file->getClientOriginalExtension())
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

    private static function fillFromIsbn(Get $get, Set $set): void
    {
        $isbn = (string) $get('isbn');

        if (blank($isbn)) {
            Notification::make()
                ->title('Masukkan ISBN dulu')
                ->warning()
                ->send();

            return;
        }

        $metadata = app(BookMetadataLookup::class)->lookup($isbn);

        if (! $metadata) {
            Notification::make()
                ->title('Data ISBN tidak ditemukan')
                ->body('Coba cek kembali nomor ISBN atau isi data buku secara manual.')
                ->warning()
                ->send();

            return;
        }

        $set('title', $metadata['title'], shouldCallUpdatedHooks: true);
        $set('slug', Str::slug($metadata['title']));
        $set('description', $metadata['description']);
        $set('publish_year', $metadata['publish_year']);
        $set('pages', $metadata['pages']);
        $set('language', $metadata['language'] ?? 'id');

        if ($metadata['publisher']) {
            $publisher = Publisher::query()->firstOrCreate([
                'name' => $metadata['publisher'],
            ]);

            $set('publisher_id', $publisher->getKey());
        }

        if ($metadata['authors'] !== []) {
            $authorIds = collect($metadata['authors'])
                ->map(fn (string $name): int => Author::query()->firstOrCreate(['name' => $name])->getKey())
                ->all();

            $set('authors', $authorIds);
        }

        if ($metadata['cover_path']) {
            $set('cover', $metadata['cover_path']);
        }

        Notification::make()
            ->title('Data buku berhasil diisi')
            ->body('Sumber data: '.$metadata['source'].'.')
            ->success()
            ->send();
    }
}
