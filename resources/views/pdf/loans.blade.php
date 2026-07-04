<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-size: 11px; }
        td { font-size: 10px; }
        .footer { margin-top: 20px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode Pinjam</th>
                <th>Peminjam</th>
                <th>NISN</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Dikembalikan</th>
                <th>Status</th>
                <th>Buku</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
            <tr>
                <td>{{ $loan->loan_code }}</td>
                <td>{{ $loan->borrower_name }}</td>
                <td>{{ $loan->borrower_nisn }}</td>
                <td>{{ $loan->loan_date }}</td>
                <td>{{ $loan->due_date }}</td>
                <td>{{ $loan->returned_at }}</td>
                <td>{{ $loan->status }}</td>
                <td>{{ $loan->books }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
