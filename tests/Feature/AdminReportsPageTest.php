<?php

use Illuminate\Support\Facades\Route;

test('admin reports page route is registered', function () {
    expect(Route::has('filament.admin.pages.laporan'))->toBeTrue();
});

test('admin reports print route is registered', function () {
    expect(Route::has('admin.reports.print'))->toBeTrue();
});
