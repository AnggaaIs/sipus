<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::query()
            ->whereIn('email', [
                'budi.santoso@sipus.com',
                'siti.rahmawati@sipus.com',
                'andi.pratama@sipus.com',
                'nabila.putri@sipus.com',
            ])
            ->get()
            ->keyBy('email');

        $books = Book::query()->orderBy('id')->get();

        $borrowedLoan = Loan::query()->create([
            'user_id' => $members['budi.santoso@sipus.com']->getKey(),
            'loan_code' => 'SIPUS-DEMO-001',
            'loan_date' => now()->subDays(2),
            'due_date' => today()->addDays(5),
            'notes' => 'Contoh peminjaman aktif untuk presentasi.',
        ]);
        $borrowedLoan->loanItems()->createMany([
            ['book_id' => $books[0]->getKey(), 'quantity' => 1],
            ['book_id' => $books[1]->getKey(), 'quantity' => 1],
        ]);
        $borrowedLoan->checkoutBooks();

        $overdueLoan = Loan::query()->create([
            'user_id' => $members['siti.rahmawati@sipus.com']->getKey(),
            'loan_code' => 'SIPUS-DEMO-002',
            'loan_date' => now()->subDays(12),
            'due_date' => today()->subDays(5),
            'notes' => 'Contoh peminjaman terlambat dengan denda belum dibayar.',
        ]);
        $overdueLoan->loanItems()->create([
            'book_id' => $books[2]->getKey(),
            'quantity' => 1,
        ]);
        $overdueLoan->checkoutBooks();
        $overdueLoan->syncFine();

        $returnedLoan = Loan::query()->create([
            'user_id' => $members['andi.pratama@sipus.com']->getKey(),
            'loan_code' => 'SIPUS-DEMO-003',
            'loan_date' => now()->subDays(14),
            'due_date' => today()->subDays(7),
            'returned_at' => now()->subDays(8),
            'notes' => 'Contoh pengembalian buku dalam kondisi baik.',
        ]);
        $returnedLoan->loanItems()->createMany([
            ['book_id' => $books[3]->getKey(), 'quantity' => 1, 'returned_at' => $returnedLoan->returned_at, 'condition_on_return' => 'good'],
            ['book_id' => $books[4]->getKey(), 'quantity' => 1, 'returned_at' => $returnedLoan->returned_at, 'condition_on_return' => 'good'],
        ]);

        $paidFineLoan = Loan::query()->create([
            'user_id' => $members['nabila.putri@sipus.com']->getKey(),
            'loan_code' => 'SIPUS-DEMO-004',
            'loan_date' => now()->subDays(20),
            'due_date' => today()->subDays(13),
            'returned_at' => now()->subDays(10),
            'notes' => 'Contoh keterlambatan yang dendanya sudah dilunasi.',
        ]);
        $paidFineLoan->loanItems()->create([
            'book_id' => $books[5]->getKey(),
            'quantity' => 1,
            'returned_at' => $paidFineLoan->returned_at,
            'condition_on_return' => 'damaged',
        ]);
        $paidFineLoan->syncFine();
        $paidFineLoan->settleFine();
    }
}
