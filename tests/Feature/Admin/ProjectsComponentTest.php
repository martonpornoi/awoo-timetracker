<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Projects;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectsComponentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Confirms the Livewire component persists a new project with admin ownership.
     */
    public function test_admin_can_create_project(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        Livewire::test(Projects::class)
            ->set('name', 'Bookkeeping')
            ->set('is_active', true)
            ->call('save')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('projects', [
            'name' => 'Bookkeeping',
            'owner_id' => $admin->id,
            'is_active' => true,
        ]);
    }

    /**
     * Ensures editing persists changes and toggleActive flips the boolean state.
     */
    public function test_project_can_be_edited_and_toggled(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $project = Project::factory()->create(['is_active' => true]);

        $this->actingAs($admin);

        Livewire::test(Projects::class)
            ->call('edit', $project->id)
            ->set('name', 'Renamed Project')
            ->set('is_active', false)
            ->call('save')
            ->call('toggleActive', $project->id);

        $project->refresh();

        $this->assertSame('Renamed Project', $project->name);
        $this->assertTrue((bool) $project->is_active);
    }

    /**
     * Validates the component emits errors when required data is missing.
     */
    public function test_validation_errors_are_reported(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        Livewire::test(Projects::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }
}
