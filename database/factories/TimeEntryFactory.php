<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        $date = Carbon::parse(
            fake()->dateTimeBetween('-2 months', 'now')
        )->toDateString();

        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'date' => $date,
            'minutes' => fake()->numberBetween(1, 12) * 15,
            'description' => fake()->sentence(),
            'locked_by_report_id' => null,
        ];
    }

    public function forMonth(string $month): static
    {
        return $this->state(fn () => [
            'date' => Carbon::parse("{$month}-01")
                ->addDays(random_int(0, 27))
                ->toDateString(),
        ]);
    }
}
