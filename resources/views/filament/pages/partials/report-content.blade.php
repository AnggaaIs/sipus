@if ($report->type() === 'summary')
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($report->summaryCards() as $card)
            <x-filament::section compact>
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-600">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold tracking-tight text-gray-950">{{ $card['value'] }}</p>
                    <p class="text-sm text-gray-500">{{ $card['note'] }}</p>
                </div>
            </x-filament::section>
        @endforeach
    </div>
@endif

@if ($report->type() === 'books')
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Judul</th>
                    <th class="py-3 pr-4">ISBN</th>
                    <th class="py-3 pr-4">Penulis</th>
                    <th class="py-3 pr-4">Kategori</th>
                    <th class="py-3 pr-4">Penerbit</th>
                    <th class="py-3 text-right">Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($report->books() as $book)
                    <tr>
                        <td class="py-3 pr-4 font-semibold text-gray-950">{{ $book->title }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $book->isbn ?: '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $book->authors->pluck('name')->join(', ') ?: '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $book->category?->name ?: '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $book->publisher?->name ?: '-' }}</td>
                        <td class="py-3 text-right text-gray-900">{{ $book->available_copies }}/{{ $book->total_copies }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data buku.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

@if ($report->type() === 'loans')
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Kode</th>
                    <th class="py-3 pr-4">Siswa</th>
                    <th class="py-3 pr-4">Buku</th>
                    <th class="py-3 pr-4">Tanggal pinjam</th>
                    <th class="py-3 pr-4">Jatuh tempo</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($report->loans() as $loan)
                    <tr>
                        <td class="py-3 pr-4 font-semibold text-gray-950">{{ $loan->loan_code }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $report->userName($loan->user) }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $report->loanBooks($loan) }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $loan->loan_date?->translatedFormat('d M Y') ?: '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $loan->due_date?->translatedFormat('d M Y') ?: '-' }}</td>
                        <td class="py-3 text-gray-900">{{ $report->statusLabel($loan->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data peminjaman pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

@if ($report->type() === 'returns')
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Kode</th>
                    <th class="py-3 pr-4">Siswa</th>
                    <th class="py-3 pr-4">Buku</th>
                    <th class="py-3 pr-4">Tanggal pinjam</th>
                    <th class="py-3 pr-4">Dikembalikan</th>
                    <th class="py-3">Kondisi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($report->returns() as $loan)
                    <tr>
                        <td class="py-3 pr-4 font-semibold text-gray-950">{{ $loan->loan_code }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $report->userName($loan->user) }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $report->loanBooks($loan) }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $loan->loan_date?->translatedFormat('d M Y') ?: '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $loan->returned_at?->translatedFormat('d M Y H:i') ?: '-' }}</td>
                        <td class="py-3 text-gray-900">{{ $report->returnConditions($loan) ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data pengembalian pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

@if ($report->type() === 'fines')
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Kode pinjam</th>
                    <th class="py-3 pr-4">Siswa</th>
                    <th class="py-3 pr-4 text-right">Hari</th>
                    <th class="py-3 pr-4 text-right">Per hari</th>
                    <th class="py-3 pr-4 text-right">Total</th>
                    <th class="py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($report->fines() as $fine)
                    <tr>
                        <td class="py-3 pr-4 font-semibold text-gray-950">{{ $fine->loan?->loan_code ?: '-' }}</td>
                        <td class="py-3 pr-4 text-gray-600">{{ $report->userName($fine->user) }}</td>
                        <td class="py-3 pr-4 text-right text-gray-600">{{ number_format($fine->overdue_days) }}</td>
                        <td class="py-3 pr-4 text-right text-gray-600">{{ $report->formatCurrency((float) $fine->amount_per_day) }}</td>
                        <td class="py-3 pr-4 text-right text-gray-900">{{ $report->formatCurrency((float) $fine->total_amount) }}</td>
                        <td class="py-3 text-gray-900">{{ $report->statusLabel($fine->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data denda pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
