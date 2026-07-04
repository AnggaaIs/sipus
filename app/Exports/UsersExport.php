<?php

namespace App\Exports;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return User::query()
            ->get()
            ->map(fn (User $user) => (object) [
                'nisn' => $user->nisn ?? '-',
                'name' => $user->name,
                'full_name' => $user->full_name ?? '-',
                'email' => $user->email,
                'class' => $user->class ?? '-',
                'role' => $user->role === 'admin' ? 'Admin' : 'Siswa',
                'account_status' => match ($user->account_status) {
                    'active' => 'Aktif',
                    'pending' => 'Menunggu',
                    'rejected' => 'Ditolak',
                    'suspended' => 'Ditangguhkan',
                    default => $user->account_status,
                },
                'approved_at' => $user->approved_at?->format('d/m/Y H:i') ?? '-',
                'phone' => $user->phone ?? '-',
            ]);
    }

    public function headings(): array
    {
        return [
            'NISN', 'Username', 'Nama Lengkap', 'Email', 'Kelas',
            'Peran', 'Status', 'Disetujui Pada', 'Telepon',
        ];
    }

    /** @param object $row */
    public function map($row): array
    {
        return [
            $row->nisn, $row->name, $row->full_name, $row->email,
            $row->class, $row->role, $row->account_status,
            $row->approved_at, $row->phone,
        ];
    }

    public static function pdf(): mixed
    {
        $data = (new self)->collection();

        return Pdf::loadView('pdf.users', ['users' => $data])
            ->download('laporan-pengguna.pdf');
    }

    public static function xlsx(): mixed
    {
        return Excel::download(
            new self,
            'laporan-pengguna.xlsx',
        );
    }
}
