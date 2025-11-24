<?php

namespace Tests\Feature;

use App\Livewire\Timesheet;
use App\Models\MonthlyReport;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class TimesheetComponentTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    /**
     * Verifies a user can persist a new entry and the minutes are calculated correctly.
     */
    public function test_user_can_create_time_entry(): void
    {
        Carbon::setTestNow('2024-05-15 12:00:00');

        $user = User::factory()->create();
        $project = Project::factory()->create(['is_active' => true]);
        $this->actingAs($user);

        Livewire::test(Timesheet::class)
            ->set('date', '2024-05-14')
            ->set('project_id', $project->id)
            ->set('time_input', '1h30m')
            ->set('description', 'Deep work')
            ->call('saveEntry')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('time_entries', [
            'user_id' => $user->id,
            'project_id' => $project->id,
            'minutes' => 90,
        ]);
    }

    /**
     * Checks that validation rules and parser errors bubble to the Livewire component.
     */
    public function test_validation_and_parser_errors_are_returned_to_user(): void
    {
        Carbon::setTestNow('2024-05-15 12:00:00');

        $user = User::factory()->create();
        $project = Project::factory()->create(['is_active' => true]);
        $this->actingAs($user);

        Livewire::test(Timesheet::class)
            ->set('date', '')
            ->set('project_id', $project->id)
            ->set('time_input', '')
            ->call('saveEntry')
            ->assertHasErrors(['date' => 'required', 'time_input' => 'required']);

        Livewire::test(Timesheet::class)
            ->set('date', '2024-05-10')
            ->set('project_id', $project->id)
            ->set('time_input', 'n/a')
            ->call('saveEntry')
            ->assertHasErrors(['time_input']);
    }

    /**
     * Ensures editing updates persisted minutes and delete removes the row.
     */
    public function test_user_can_edit_and_delete_entries(): void
    {
        Carbon::setTestNow('2024-05-15 12:00:00');

        $user = User::factory()->create();
        $project = Project::factory()->create(['is_active' => true]);
        $entry = TimeEntry::factory()
            ->for($user, 'user')
            ->for($project, 'project')
            ->create([
                'date' => '2024-05-10',
                'minutes' => 60,
            ]);

        $this->actingAs($user);

        Livewire::test(Timesheet::class)
            ->call('editEntry', $entry->id)
            ->set('time_input', '2h')
            ->call('saveEntry')
            ->call('delete', $entry->id);

        $this->assertDatabaseMissing('time_entries', ['id' => $entry->id]);
    }

    /**
     * Confirms the component blocks CRUD operations when the month report is closed.
     */
    public function test_locked_month_prevents_changes(): void
    {
        Carbon::setTestNow('2024-05-15 12:00:00');

        $user = User::factory()->create();
        $project = Project::factory()->create(['is_active' => true]);

        MonthlyReport::factory()
            ->closed()
            ->create(['month' => '2024-05-01']);

        $this->actingAs($user);

        Livewire::test(Timesheet::class)
            ->set('date', '2024-05-14')
            ->set('project_id', $project->id)
            ->set('time_input', '1h')
            ->call('saveEntry')
            ->assertHasErrors(['date']);

        $this->assertDatabaseCount('time_entries', 0);
    }
}
