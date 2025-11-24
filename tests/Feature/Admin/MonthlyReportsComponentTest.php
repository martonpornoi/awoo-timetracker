<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MonthlyReports;
use App\Models\MonthlyReport;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class MonthlyReportsComponentTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * Validates generate aggregates entries, close locks them, and reopen frees only its rows.
     */
    public function test_admin_can_generate_and_lock_reports(): void
    {
        Carbon::setTestNow('2024-05-20 12:00:00');

        $admin = User::factory()->create(['is_admin' => true]);
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        TimeEntry::factory()->for($userOne, 'user')->for($projectA, 'project')->create([
            'date' => '2024-05-01',
            'minutes' => 60,
        ]);
        TimeEntry::factory()->for($userOne, 'user')->for($projectB, 'project')->create([
            'date' => '2024-05-02',
            'minutes' => 45,
        ]);
        TimeEntry::factory()->for($userTwo, 'user')->for($projectA, 'project')->create([
            'date' => '2024-05-03',
            'minutes' => 30,
        ]);

        $this->actingAs($admin);

        $component = Livewire::test(MonthlyReports::class)
            ->set('month', '2024-05')
            ->call('generate');

        $report = MonthlyReport::first();
        $this->assertNotNull($report);
        $this->assertSame(135, $report->snapshot['total_minutes']);
        $this->assertSame(105, $report->snapshot['by_user'][$userOne->id]['minutes']);
        $this->assertSame(90, $report->snapshot['by_project'][$projectA->id]['minutes']);

        $component->call('close');

        $report->refresh();
        $this->assertSame('closed', $report->status);
        $this->assertTrue(
            TimeEntry::whereBetween('date', ['2024-05-01', '2024-05-31'])
                ->get()
                ->every(fn ($entry) => $entry->locked_by_report_id === $report->id)
        );

        $otherReport = MonthlyReport::factory()->closed()->create(['month' => '2024-04-01']);
        $externalEntry = TimeEntry::factory()->for($userOne, 'user')->for($projectA, 'project')->create([
            'date' => '2024-04-15',
            'minutes' => 15,
            'locked_by_report_id' => $otherReport->id,
        ]);

        $component->call('reopen');

        $report->refresh();
        $this->assertSame('open', $report->status);
        $this->assertTrue(
            TimeEntry::whereBetween('date', ['2024-05-01', '2024-05-31'])
                ->get()
                ->every(fn ($entry) => $entry->locked_by_report_id === null)
        );
        $this->assertSame($otherReport->id, $externalEntry->fresh()->locked_by_report_id);
    }

    /**
     * Ensures invoking generate twice updates the snapshot instead of failing unique constraints.
     */
    public function test_generate_rebuilds_snapshot_when_called_multiple_times(): void
    {
        Carbon::setTestNow('2024-05-20 12:00:00');

        $admin = User::factory()->create(['is_admin' => true]);
        $project = Project::factory()->create();
        $entry = TimeEntry::factory()->for($project, 'project')->create([
            'date' => '2024-05-05',
            'minutes' => 30,
        ]);

        $this->actingAs($admin);

        $component = Livewire::test(MonthlyReports::class)
            ->set('month', '2024-05')
            ->call('generate');

        $report = MonthlyReport::first();
        $this->assertSame(30, $report->snapshot['total_minutes']);

        $entry->update(['minutes' => 90]);

        $component->call('generate');

        $report->refresh();
        $this->assertSame(90, $report->snapshot['total_minutes']);
    }
}
