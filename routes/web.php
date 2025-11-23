<?php

use App\Models\MonthlyReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'welcome')->name('profile');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::view('/timesheet', 'timesheet-page')->name('timesheet');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        // Admin pages
        Route::view('/reports', 'admin.monthly-reports-page')->name('admin.reports');
        Route::view('/projects', 'admin.projects-page')->name('admin.projects');

        // CSV download
        Route::get('/reports/{report}/csv', function (MonthlyReport $report) {
            abort_unless(Auth::user()?->isAdmin(), 403);

            if (! $report->export_path || ! Storage::disk('local')->exists($report->export_path)) {
                abort(404);
            }

            return Storage::disk('local')->download(
                $report->export_path,
                'report_' . $report->month->format('Y-m') . '.csv'
            );
        })->name('admin.reports.csv');
    });

require __DIR__.'/auth.php';
