<?php

namespace Tests\Feature\Admin;

use App\Models\MonthlyReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensures a regular authenticated user receives 403 when visiting admin routes.
     */
    public function test_non_admin_is_blocked_from_admin_pages(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/projects')
            ->assertForbidden();
    }

    /**
     * Verifies that an administrator can reach the admin dashboard endpoints.
     */
    public function test_admin_can_view_admin_pages(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/projects')
            ->assertOk();
    }

    /**
     * Confirms the CSV endpoint enforces the admin middleware stack.
     */
    public function test_csv_download_requires_admin_privileges(): void
    {
        $report = MonthlyReport::factory()->create();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.reports.csv', $report))
            ->assertForbidden();
    }

    /**
     * Validates that a stored export is returned with the expected filename.
     */
    public function test_csv_download_returns_file_when_present(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['is_admin' => true]);
        $report = MonthlyReport::factory()->create([
            'export_path' => 'reports/report.csv',
        ]);

        Storage::disk('local')->put('reports/report.csv', 'csv-data');

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.csv', $report));

        $response->assertOk();
        $response->assertDownload(
            'report_' . $report->month->format('Y-m') . '.csv'
        );
    }

    /**
     * Asserts that the CSV route fails with 404 when the stored file is absent.
     */
    public function test_csv_download_returns_404_when_missing_file(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['is_admin' => true]);
        $report = MonthlyReport::factory()->create([
            'export_path' => 'reports/missing.csv',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports.csv', $report))
            ->assertNotFound();
    }
}
