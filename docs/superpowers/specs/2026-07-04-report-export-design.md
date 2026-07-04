# Report Export Feature — SIPUS

## Overview

Add export functionality (PDF + XLSX) to the Filament Admin Panel for 4 resources: Loans, Books, Fines, and Users. Export triggers a direct file download with no additional database tables.

## Architecture

```
User clicks Export → Modal (choose PDF/XLSX) → Generate in-memory → Stream download
```

No queue, no database tables, no notifications — direct synchronous generation.

## Packages

| Package | Purpose | Status |
|---------|---------|--------|
| `barryvdh/laravel-dompdf` | PDF generation | Already installed |
| `maatwebsite/laravel-excel` | XLSX generation | To install |

## Files to Create

### Export Classes (app/Exports/)

Each class handles both PDF and XLSX generation for its resource type.

| File | Data Exported |
|------|---------------|
| `LoansExport.php` | loan_code, user (name/NISN), loan_date, due_date, returned_at, status, items |
| `BooksExport.php` | ISBN, title, authors, category, DDC, publisher, total_copies, available_copies |
| `FinesExport.php` | loan_code, user, overdue_days, amount_per_day, total_amount, status, paid_at |
| `UsersExport.php` | NISN, name, email, class, role, account_status, approved_at, phone |

### PDF Blade Templates (resources/views/pdf/)

- `loans.blade.php`
- `books.blade.php`
- `fines.blade.php`
- `users.blade.php`

Each template includes: header with title/date, styled table, footer.

### Filament Export Action

A custom Filament `Action` added as a `headerAction()` in each resource. The action opens a modal with two buttons:
- **Export PDF** → triggers DomPDF generation and download
- **Export XLSX** → triggers Laravel Excel generation and download

## Files to Modify

| File | Change |
|------|--------|
| `app/Filament/Admin/Resources/Loans/LoanResource.php` | Add header action |
| `app/Filament/Admin/Resources/Books/BookResource.php` | Add header action |
| `app/Filament/Admin/Resources/Fines/FineResource.php` | Add header action |
| `app/Filament/Admin/Resources/Users/UserResource.php` | Add header action |

## Implementation Steps

1. Install `maatwebsite/laravel-excel`
2. Create Filament export action (reusable trait or helper)
3. Create export classes (4 files)
4. Create PDF Blade templates (4 files)
5. Add header actions to resources (4 files)
6. Create tests for export
7. Update PPKPL documentation

## Testing

- Feature tests for each export type
- Verify PDF downloads with correct content type
- Verify XLSX downloads with correct content type
- Verify data in exported files matches database

## PPKPL Docs to Update

- `ppkpl/Laporan_Pengujian_PPKPL_SIPUS.md` — add test cases for export feature
- `ppkpl/Bukti_Eksekusi_Automated_Testing.md` — update test execution results
