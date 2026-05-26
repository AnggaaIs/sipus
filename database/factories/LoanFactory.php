<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loanDate = fake()->dateTimeBetween('-14 days', 'now');
        $dueDate = (clone $loanDate)->modify('+7 days');

        return [
            'user_id' => User::factory()->member(),
            'loan_code' => 'LOAN-'.strtoupper(Str::random(8)),
            'loan_date' => $loanDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'returned_at' => null,
            'status' => 'borrowed',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
