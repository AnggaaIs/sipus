<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Denda</title>
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
    <h1>Laporan Denda</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode Pinjam</th>
                <th>Siswa</th>
                <th>NISN</th>
                <th>Hari Telat</th>
                <th>Denda/Hari</th>
                <th>Total Denda</th>
                <th>Status</th>
                <th>Dibayar Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fines as $fine)
            <tr>
                <td>{{ $fine->loan_code }}</td>
                <td>{{ $fine->user_name }}</td>
                <td>{{ $fine->user_nisn }}</td>
                <td>{{ $fine->overdue_days }}</td>
                <td>Rp {{ $fine->amount_per_day }}</td>
                <td>Rp {{ $fine->total_amount }}</td>
                <td>{{ $fine->status }}</td>
                <td>{{ $fine->paid_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
