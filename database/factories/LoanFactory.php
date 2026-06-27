<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $loanDate = Carbon::instance(fake()->dateTimeBetween('-7 days', 'now'));

        return [
            'user_id' => User::factory()->member(),
            'loan_code' => 'SIPUS-'.now()->format('Ymd').'-'.Str::upper(fake()->unique()->bothify('?????')),
            'loan_date' => $loanDate,
            'due_date' => $loanDate->copy()->addDays(7)->toDateString(),
            'returned_at' => null,
            'status' => Loan::STATUS_BORROWED,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function returned(): static
    {
        return $this->state(function (array $attributes): array {
            $loanDate = Carbon::parse($attributes['loan_date']);

            return [
                'due_date' => $loanDate->copy()->addDays(7)->toDateString(),
                'returned_at' => $loanDate->copy()->addDays(3),
                'status' => Loan::STATUS_RETURNED,
            ];
        });
    }

    public function overdue(): static
    {
        return $this->state(function (array $attributes): array {
            $loanDate = Carbon::parse($attributes['loan_date']);

            return [
                'due_date' => $loanDate->copy()->addDays(1)->toDateString(),
                'returned_at' => null,
                'status' => Loan::STATUS_OVERDUE,
            ];
        });
    }
}
