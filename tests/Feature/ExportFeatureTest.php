<?php

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    actingAs($this->admin);
});

test('halaman index loans dapat diakses', function () {
    get('/admin/loans')->assertOk();
});

test('halaman index books dapat diakses', function () {
    get('/admin/books')->assertOk();
});

test('halaman index fines dapat diakses', function () {
    get('/admin/fines')->assertOk();
});

test('halaman index users dapat diakses', function () {
    get('/admin/users')->assertOk();
});

test('tombol export muncul di halaman loans', function () {
    get('/admin/loans')->assertSee('Export');
});

test('tombol export muncul di halaman books', function () {
    get('/admin/books')->assertSee('Export');
});

test('tombol export muncul di halaman fines', function () {
    get('/admin/fines')->assertSee('Export');
});

test('tombol export muncul di halaman users', function () {
    get('/admin/users')->assertSee('Export');
});

test('loans export class menghasilkan data dengan struktur yang benar', function () {
    $user = User::factory()->member()->create();
    $book = Book::factory()->create();
    $loan = Loan::factory()->create(['user_id' => $user->getKey()]);
    $loan->loanItems()->create(['book_id' => $book->getKey(), 'quantity' => 1]);

    $export = new \App\Exports\LoansExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['loan_code', 'borrower_name', 'status']);
});

test('books export class menghasilkan data dengan struktur yang benar', function () {
    Book::factory()->create();

    $export = new \App\Exports\BooksExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['isbn', 'title', 'authors', 'total_copies']);
});

test('fines export class menghasilkan data dengan struktur yang benar', function () {
    $user = User::factory()->member()->create();
    $loan = Loan::factory()->create(['user_id' => $user->getKey()]);
    Fine::factory()->create([
        'loan_id' => $loan->getKey(),
        'user_id' => $user->getKey(),
    ]);

    $export = new \App\Exports\FinesExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['loan_code', 'user_name', 'total_amount']);
});

test('users export class menghasilkan data dengan struktur yang benar', function () {
    User::factory()->member()->create();

    $export = new \App\Exports\UsersExport;
    $collection = $export->collection();

    expect($collection)->not->toBeEmpty()
        ->and($collection->first())->toHaveProperties(['nisn', 'name', 'email', 'role']);
});
