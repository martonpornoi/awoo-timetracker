<?php

namespace Database\Factories;

use App\Models\MonthlyReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<MonthlyReport>
 */
class MonthlyReportFactory extends Factory
{
    protected $model = MonthlyReport::class;

    public function definition(): array
    {
        $month = Carbon::parse(
            fake()->dateTimeBetween('-6 months', 'now')
        )->startOfMonth();

        return [
            'month' => $month,
            'status' => 'open',
            'created_by' => User::factory(),
            'snapshot' => null,
            'export_path' => null,
            'closed_at' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'closed_at' => $attributes['closed_at'] ?? Carbon::now(),
        ]);
    }
}
