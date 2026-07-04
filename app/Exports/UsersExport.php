<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;

class UsersExport extends BaseExport
{
    protected function title(): string
    {
        return 'Laporan Data Pengguna';
    }

    protected function fileName(): string
    {
        return 'laporan-pengguna';
    }

    protected function columnCount(): int
    {
        return 9;
    }

    protected function statusColumn(): ?string
    {
        return 'G';
    }

    protected function statusColors(): array
    {
        return [
            'Aktif' => 'FF44FF44',
            'Menunggu' => 'FFFFFF44',
            'Ditolak' => 'FFFF4444',
            'Ditangguhkan' => 'FFFF8800',
        ];
    }

    public function collection(): Collection
    {
        return User::query()
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate))
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
            'Peran', 'Status Akun', 'Disetujui Pada', 'Telepon',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nisn, $row->name, $row->full_name, $row->email,
            $row->class, $row->role, $row->account_status,
            $row->approved_at, $row->phone,
        ];
    }
}
