<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\LibraryReportData;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportPrintController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless(
            $request->user()?->canAccessPanel(Filament::getPanel('admin')) ?? false,
            403,
        );

        return view('filament.pages.reports-print', [
            'report' => new LibraryReportData(
                reportType: (string) $request->query('jenis', 'summary'),
                startDate: $request->query('dari') ? (string) $request->query('dari') : null,
                endDate: $request->query('sampai') ? (string) $request->query('sampai') : null,
            ),
        ]);
    }
}
