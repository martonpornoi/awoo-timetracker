<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            ['name' => 'Super Admin', 'email' => 'super1@example.com'],
            ['name' => 'Ops Admin', 'email' => 'super2@example.com'],
        ];

        $users = collect($admins)->map(function (array $admin) {
            return User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => true,
                ]
            );
        });

        $userAccounts = collect([
            ['name' => 'Demo User A', 'email' => 'user1@example.com'],
            ['name' => 'Demo User B', 'email' => 'user2@example.com'],
        ])->map(function (array $user) {
            return User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ]
            );
        });

        $projects = collect([
            ['name' => 'Alpha Portal', 'is_active' => true],
            ['name' => 'Beta Console', 'is_active' => true],
            ['name' => 'Legacy Archive', 'is_active' => false],
        ])->map(function (array $proj, int $index) use ($users) {
            return Project::updateOrCreate(
                ['name' => $proj['name']],
                [
                    'code' => strtoupper(substr($proj['name'], 0, 3)) . '-' . ($index + 1),
                    'is_active' => $proj['is_active'],
                    'owner_id' => $users->first()?->id,
                ]
            );
        });

        $allUsers = $users->merge($userAccounts);
        $months = collect([
            Carbon::now()->startOfMonth(),
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonths(2)->startOfMonth(),
        ]);

        $months->each(function (Carbon $month) use ($allUsers, $projects) {
            foreach (range(1, 15) as $day) {
                $date = $month->copy()->addDays(($day - 1) % $month->daysInMonth);
                $entryUser = $allUsers->random();
                $project = $projects->random();

                TimeEntry::factory()->create([
                    'user_id' => $entryUser->id,
                    'project_id' => $project->id,
                    'date' => $date->toDateString(),
                    'minutes' => collect([30, 45, 60, 90, 120])->random(),
                    'description' => sprintf('Worked on %s tasks', $project->name),
                ]);
            }
        });
    }
}
