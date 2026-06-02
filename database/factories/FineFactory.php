<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $overdueDays = fake()->numberBetween(1, 14);
        $amountPerDay = 1000;

        return [
            'loan_id' => Loan::factory(),
            'user_id' => User::factory()->member(),
            'overdue_days' => $overdueDays,
            'amount_per_day' => $amountPerDay,
            'total_amount' => $overdueDays * $amountPerDay,
            'status' => 'unpaid',
            'paid_at' => null,
        ];
    }
}
