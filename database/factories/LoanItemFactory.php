<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanItem>
 */
class LoanItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'book_id' => Book::factory(),
            'quantity' => 1,
            'returned_at' => null,
            'condition_on_return' => null,
        ];
    }
}
