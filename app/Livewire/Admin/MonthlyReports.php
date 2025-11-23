<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\MonthlyReport;
use App\Models\TimeEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyReports extends Component
{
    public $month;           // YYYY-MM
    public $report;          // MonthlyReport|null
    public $snapshot = [];   // array for display

    public function mount()
    {
        $this->month = now()->format('Y-m');
        $this->loadReport();
    }

    /** Load or refresh the monthly report. */
    public function loadReport()
    {
        $date = Carbon::parse($this->month . '-01');
        $this->report = MonthlyReport::firstWhere('month', $date->format('Y-m-d'));

        if ($this->report) {
            $this->snapshot = $this->report->snapshot ?: [];
        } else {
            $this->snapshot = [];
        }
    }

    /** Regenerate monthly data */
    public function generate()
    {
        $date = Carbon::parse($this->month . '-01');

        // Fetch all entries
        $entries = TimeEntry::with('project', 'user')
            ->whereBetween('date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->get();

        // Build our monthly snapshot
        $snapshot = [
            'total_minutes' => $entries->sum('minutes'),

            'by_user' => $entries
                ->groupBy('user_id')
                ->map(fn(Collection $userRows) => [
                    'minutes' => $userRows->sum('minutes'),
                    'projects' => $userRows->groupBy('project_id')
                        ->map(fn(Collection $projRows) => [
                            'minutes' => $projRows->sum('minutes'),
                        ]),
                ])
                ->toArray(),

            'by_project' => $entries
                ->groupBy('project_id')
                ->map(fn(Collection $projRows) => [
                    'minutes' => $projRows->sum('minutes'),
                ])
                ->toArray(),
        ];

        // Create or update report
        $this->report = MonthlyReport::updateOrCreate(
            attributes: ['month' => $date->format('Y-m-d')],
            values: [
                'snapshot' => $snapshot,
                'created_by' => auth()->id(),
                'status' => $this->report?->status ?? 'open', // keep status if already exists
            ]
        );
        $this->snapshot = $snapshot;
    }

    /** Close / lock the report + entries */
    public function close()
    {
        if (!$this->report) return;

        DB::transaction(function () {
            // 1) Close report
            $this->report->update(['status' => 'closed']);

            // 2) Lock all entries in that month that aren't locked yet
            $start = Carbon::parse($this->report->month)->startOfMonth()->toDateString();
            $end   = Carbon::parse($this->report->month)->endOfMonth()->toDateString();

            TimeEntry::whereBetween('date', [$start, $end])
                ->whereNull('locked_by_report_id')
                ->update(['locked_by_report_id' => $this->report->id]);
        });

        $this->loadReport();
    }

    /** Re-open the report + unlock entries locked by this report */
    public function reopen()
    {
        if (!$this->report) return;

        DB::transaction(function () {
            // 1) Open report
            $this->report->update(['status' => 'open']);

            // 2) Unlock only entries that were locked by THIS report
            TimeEntry::where('locked_by_report_id', $this->report->id)
                ->update(['locked_by_report_id' => null]);
        });

        $this->loadReport();
    }

    public function updatedMonth()
    {
        $this->loadReport();
    }

    public function render()
    {
        return view('livewire.admin.monthly-reports', [
            'report' => $this->report,
            'snapshot' => $this->snapshot,
        ]);
    }
}
