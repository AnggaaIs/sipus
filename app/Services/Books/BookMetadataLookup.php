<?php

namespace App\Services\Books;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BookMetadataLookup
{
    /**
     * @return array{
     *     title: string,
     *     description: ?string,
     *     publish_year: ?int,
     *     pages: ?int,
     *     language: ?string,
     *     publisher: ?string,
     *     authors: list<string>,
     *     cover_path: ?string,
     *     source: string,
     * }|null
     */
    public function lookup(string $isbn): ?array
    {
        $isbn = $this->normalizeIsbn($isbn);

        if ($isbn === '') {
            return null;
        }

        $googleBooks = $this->lookupGoogleBooks($isbn);
        $openLibrary = $this->lookupOpenLibrary($isbn);

        if ($googleBooks === null && $openLibrary === null) {
            return null;
        }

        $title = $googleBooks['title'] ?? $openLibrary['title'] ?? null;

        if ($title === null) {
            return null;
        }

        $coverUrl = $googleBooks['cover_url'] ?? $openLibrary['cover_url'] ?? null;
        $sources = collect([
            $googleBooks['source'] ?? null,
            $openLibrary['source'] ?? null,
        ])->filter()->unique()->values()->all();

        return [
            'title' => $title,
            'description' => $googleBooks['description'] ?? $openLibrary['description'] ?? null,
            'publish_year' => $googleBooks['publish_year'] ?? $openLibrary['publish_year'] ?? null,
            'pages' => $googleBooks['pages'] ?? $openLibrary['pages'] ?? null,
            'language' => $googleBooks['language'] ?? $openLibrary['language'] ?? null,
            'publisher' => $googleBooks['publisher'] ?? $openLibrary['publisher'] ?? null,
            'authors' => array_values(array_unique(array_merge(
                $googleBooks['authors'] ?? [],
                $openLibrary['authors'] ?? [],
            ))),
            'cover_path' => $this->storeCover($coverUrl, $isbn, $title),
            'source' => implode(' + ', $sources),
        ];
    }

    /**
     * @return array{
     *     title: string,
     *     description: ?string,
     *     publish_year: ?int,
     *     pages: ?int,
     *     language: ?string,
     *     publisher: ?string,
     *     authors: list<string>,
     *     cover_url: ?string,
     *     source: string,
     * }|null
     */
    private function lookupGoogleBooks(string $isbn): ?array
    {
        try {
            $response = $this->http()
                ->connectTimeout(5)
                ->timeout(10)
                ->get('https://www.googleapis.com/books/v1/volumes', [
                    'q' => 'isbn:'.$isbn,
                    'maxResults' => 1,
                ]);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $volume = data_get($response->json(), 'items.0.volumeInfo');
        $title = $this->cleanText(data_get($volume, 'title'));

        if (! $title) {
            return null;
        }

        $coverUrl = data_get($volume, 'imageLinks.extraLarge')
            ?? data_get($volume, 'imageLinks.large')
            ?? data_get($volume, 'imageLinks.medium')
            ?? data_get($volume, 'imageLinks.thumbnail')
            ?? data_get($volume, 'imageLinks.smallThumbnail');

        return [
            'title' => $title,
            'description' => $this->cleanText(data_get($volume, 'description')),
            'publish_year' => $this->extractYear(data_get($volume, 'publishedDate')),
            'pages' => $this->positiveInteger(data_get($volume, 'pageCount')),
            'language' => $this->normalizeLanguage(data_get($volume, 'language')),
            'publisher' => $this->cleanText(data_get($volume, 'publisher')),
            'authors' => $this->cleanList(data_get($volume, 'authors', [])),
            'cover_url' => $coverUrl,
            'source' => 'Google Books',
        ];
    }

    /**
     * @return array{
     *     title: string,
     *     description: ?string,
     *     publish_year: ?int,
     *     pages: ?int,
     *     language: ?string,
     *     publisher: ?string,
     *     authors: list<string>,
     *     cover_url: ?string,
     *     source: string,
     * }|null
     */
    private function lookupOpenLibrary(string $isbn): ?array
    {
        try {
            $response = $this->http()
                ->connectTimeout(5)
                ->timeout(10)
                ->get('https://openlibrary.org/api/books', [
                    'bibkeys' => 'ISBN:'.$isbn,
                    'format' => 'json',
                    'jscmd' => 'data',
                ]);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $book = data_get($response->json(), 'ISBN:'.$isbn);
        $title = $this->cleanText(data_get($book, 'title'));

        if (! $title) {
            return null;
        }

        $description = data_get($book, 'description.value') ?? data_get($book, 'description');
        $coverUrl = data_get($book, 'cover.large')
            ?? data_get($book, 'cover.medium')
            ?? data_get($book, 'cover.small');

        return [
            'title' => $title,
            'description' => is_string($description) ? $this->cleanText($description) : null,
            'publish_year' => $this->extractYear(data_get($book, 'publish_date')),
            'pages' => $this->positiveInteger(data_get($book, 'number_of_pages')),
            'language' => $this->normalizeLanguage(data_get($book, 'languages.0.key')),
            'publisher' => $this->cleanText(data_get($book, 'publishers.0.name')),
            'authors' => $this->cleanList(collect(data_get($book, 'authors', []))->pluck('name')->all()),
            'cover_url' => $coverUrl,
            'source' => 'Open Library',
        ];
    }

    private function storeCover(?string $coverUrl, string $isbn, string $title): ?string
    {
        if (! $coverUrl) {
            return null;
        }

        $coverUrl = Str::of($coverUrl)->replaceStart('http://', 'https://')->toString();

        try {
            $response = $this->http()->timeout(10)->get($coverUrl);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful() || $response->body() === '') {
            return null;
        }

        $extension = match (Str::of((string) $response->header('Content-Type'))->before(';')->lower()->toString()) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $path = 'isbn/'.$isbn.'-'.Str::slug($title).'.'.$extension;

        Storage::disk('covers')->put($path, $response->body());

        return $path;
    }

    private function normalizeIsbn(string $isbn): string
    {
        return Str::upper(preg_replace('/[^0-9Xx]/', '', $isbn) ?? '');
    }

    private function cleanText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim(strip_tags($value));

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<int, mixed>  $values
     * @return list<string>
     */
    private function cleanList(array $values): array
    {
        return collect($values)
            ->filter(fn (mixed $value): bool => is_string($value))
            ->map(fn (string $value): ?string => $this->cleanText($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function extractYear(mixed $value): ?int
    {
        if (! is_string($value) || ! preg_match('/\d{4}/', $value, $matches)) {
            return null;
        }

        return (int) $matches[0];
    }

    private function positiveInteger(mixed $value): ?int
    {
        if (! is_numeric($value) || (int) $value < 1) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeLanguage(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $language = Str::of($value)->afterLast('/')->lower()->toString();

        return match ($language) {
            'ind', 'id' => 'id',
            'eng', 'en' => 'en',
            default => Str::limit($language, 5, ''),
        };
    }

    private function http(): PendingRequest
    {
        $request = Http::acceptJson();

        return app()->isLocal() ? $request->withoutVerifying() : $request;
    }
}
