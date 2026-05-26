<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $report->reportTitle() }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #111827;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 32px;
        }

        header {
            border-bottom: 1px solid #d1d5db;
            display: flex;
            gap: 24px;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            margin: 8px 0;
        }

        p {
            margin: 0;
        }

        table {
            border-collapse: collapse;
            font-size: 12px;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            color: #4b5563;
            font-size: 11px;
            text-transform: uppercase;
        }

        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .fi-section {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
        }

        .text-right {
            text-align: right;
        }

        .print-actions {
            margin-bottom: 24px;
        }

        .print-actions button {
            background: #4f46e5;
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font-weight: 700;
            padding: 10px 16px;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Cetak laporan</button>
    </div>

    <header>
        <div>
            <p>SIPUS SMA Semen Padang</p>
            <h1>{{ $report->reportTitle() }}</h1>
            <p>Periode {{ $report->dateRangeLabel() }}</p>
        </div>
        <div>
            <p>Dicetak</p>
            <p>{{ $report->printedAt() }}</p>
        </div>
    </header>

    @include('filament.pages.partials.report-content', ['report' => $report])

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
