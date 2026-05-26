<?php

use App\Filament\Pages\Reports;

test('reports page uses Indonesian report labels', function () {
    $page = new Reports;

    expect($page->reportTypeOptions())->toBe([
        'summary' => 'Ringkasan',
        'books' => 'Buku',
        'loans' => 'Peminjaman',
        'returns' => 'Pengembalian',
        'fines' => 'Denda',
    ]);

    $page->reportType = 'fines';

    expect($page->reportTitle())->toBe('Laporan Denda');
});
