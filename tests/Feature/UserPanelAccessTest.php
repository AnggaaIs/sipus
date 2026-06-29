<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Ddc;
use App\Models\Loan;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Black-Box Testing
|--------------------------------------------------------------------------
| Fokus: hak akses panel admin dan user dari sudut pandang aktor sistem.
*/

test('guest yang membuka panel filament diarahkan ke halaman login publik', function () {
    $this->get(route('filament.admin.auth.profile'))
        ->assertRedirect(route('login'));

    $this->get(route('filament.user.auth.profile'))
        ->assertRedirect(route('login'));
});

test('anggota aktif bisa membuka halaman panel user', function () {
    $member = User::factory()->member()->create();

    $this->actingAs($member)
        ->get(route('filament.user.auth.profile'))
        ->assertOk();

    $this->actingAs($member)
        ->get(route('filament.user.resources.loans.index'))
        ->assertOk();

    $this->actingAs($member)
        ->get(route('filament.user.resources.fines.index'))
        ->assertOk();
});

test('anggota aktif tidak bisa membuka panel admin', function () {
    $member = User::factory()->member()->create();

    $this->actingAs($member)
        ->get(route('filament.admin.auth.profile'))
        ->assertForbidden();
});

test('admin aktif bisa membuka halaman inti pengelolaan admin', function () {
    $admin = User::factory()->admin()->create();

    $author = Author::factory()->create();
    $category = Category::factory()->create();
    $ddc = Ddc::factory()->create();
    $publisher = Publisher::factory()->create();
    $book = Book::factory()->create([
        'category_id' => $category->getKey(),
        'ddc_id' => $ddc->getKey(),
        'publisher_id' => $publisher->getKey(),
    ]);
    $managedUser = User::factory()->member()->create();
    $loan = Loan::factory()->create([
        'user_id' => $managedUser->getKey(),
    ]);

    $routes = [
        route('filament.admin.auth.profile'),
        route('filament.admin.resources.authors.index'),
        route('filament.admin.resources.authors.create'),
        route('filament.admin.resources.authors.edit', $author),
        route('filament.admin.resources.books.index'),
        route('filament.admin.resources.books.create'),
        route('filament.admin.resources.books.edit', $book),
        route('filament.admin.resources.categories.index'),
        route('filament.admin.resources.categories.create'),
        route('filament.admin.resources.categories.edit', $category),
        route('filament.admin.resources.ddcs.index'),
        route('filament.admin.resources.ddcs.create'),
        route('filament.admin.resources.ddcs.edit', $ddc),
        route('filament.admin.resources.fines.index'),
        route('filament.admin.resources.loans.index'),
        route('filament.admin.resources.loans.create'),
        route('filament.admin.resources.loans.edit', $loan),
        route('filament.admin.resources.pengembalians.index'),
        route('filament.admin.resources.publishers.index'),
        route('filament.admin.resources.publishers.create'),
        route('filament.admin.resources.publishers.edit', $publisher),
        route('filament.admin.resources.users.index'),
        route('filament.admin.resources.users.create'),
        route('filament.admin.resources.users.edit', $managedUser),
    ];

    foreach ($routes as $url) {
        $this->actingAs($admin)
            ->get($url)
            ->assertOk();
    }
});

test('anggota pending ditolak dan nonaktif tidak bisa membuka panel user', function () {
    $blockedUsers = [
        User::factory()->pendingApproval()->create(),
        User::factory()->member()->create([
            'account_status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
        ]),
        User::factory()->member()->create([
            'is_active' => false,
        ]),
    ];

    foreach ($blockedUsers as $blockedUser) {
        $this->actingAs($blockedUser)
            ->get(route('filament.user.auth.profile'))
            ->assertForbidden();
    }
});
