<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Pengguna</title>
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
    <h1>Laporan Pengguna</h1>
    <p class="subtitle">Sistem Informasi Perpustakaan SMA Semen Padang</p>
    <p>Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>NISN</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Kelas</th>
                <th>Peran</th>
                <th>Status</th>
                <th>Disetujui Pada</th>
                <th>Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->nisn }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->full_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->class }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->account_status }}</td>
                <td>{{ $user->approved_at }}</td>
                <td>{{ $user->phone }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} | SIPUS - SMA Semen Padang
    </div>
</body>
</html>
