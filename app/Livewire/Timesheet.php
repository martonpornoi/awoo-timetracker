<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\MonthlyReport;
use App\Support\TimeParser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Timesheet extends Component
{
    public $month;             // "YYYY-MM"
    public $entries;           // Collection
    public $projects;

    // Modal fields
    public $editingId = null;
    public $date;
    public $project_id;
    public $time_input;
    public $description;
    public $parsedPreview = null;
    public $errorPreview = null;
    public bool $showModal = false;

    public function mount()
    {
        $this->month = now()->format('Y-m');
        $this->projects = Project::where('is_active', true)->orderBy('name')->get();
        $this->loadEntries();
    }

    /** Computed: current month's report (if any) */
    public function getMonthlyReportProperty()
    {
        return MonthlyReport::where('month', $this->month . '-01')->first();
    }

    /** Computed: is month locked? */
    public function getIsLockedProperty()
    {
        $rep = $this->monthlyReport;
        return $rep && $rep->isClosed();
    }

    /** Computed totals */
    public function getTotalMinutesProperty(): int
    {
        return (int) $this->entries->sum('minutes');
    }

    public function getTotalHoursProperty(): float
    {
        return round($this->totalMinutes / 60, 2);
    }

    /** Group entries by day for UI */
    public function getEntriesByDateProperty(): Collection
    {
        return $this->entries->groupBy(fn($e) => $e->date->format('Y-m-d'));
    }

    public function loadEntries()
    {
        $start = $this->month . '-01';
        $end = now()->parse($start)->endOfMonth()->toDateString();

        $this->entries = TimeEntry::with('project')
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('id')
            ->get();
    }

    public function updatedMonth()
    {
        $this->loadEntries();
    }

    public function updatedTimeInput()
    {
        $this->parsedPreview = null;
        $this->errorPreview = null;

        try {
            $parsed = TimeParser::parse($this->time_input);
            $this->parsedPreview =
                $parsed['hours'] . ' h (' . $parsed['rounded_minutes'] . ' min, rounded)';
        } catch (\Throwable $e) {
            $this->errorPreview = $e->getMessage();
        }
    }

    public function openCreateModal($prefillDate = null)
    {
        if ($this->isLocked) return;

        $this->resetValidation(); // good habit so old errors don't stick

        $this->reset([
            'editingId','date','project_id','time_input','description',
            'parsedPreview','errorPreview'
        ]);

        $this->date = $prefillDate ?? now()->toDateString();

        $this->showModal = true;   // ✅ Livewire controls modal now
    }

    public function editEntry(int $id): void
    {
        if ($this->isLocked) return;

        $entry = TimeEntry::whereKey($id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->resetValidation();

        $this->editingId   = $entry->id;
        $this->date = \Carbon\Carbon::parse($entry->date)->format('Y-m-d');
        $this->project_id  = $entry->project_id;
        $this->time_input  = round($entry->minutes / 60, 2); // or format how you like
        $this->description = $entry->description;

        // you can also set previews if you want:
        $this->parsedPreview = null;
        $this->errorPreview  = null;

        $this->showModal = true;   // ✅ REQUIRED now (no Alpine)
    }

    public function save()
    {
        // proof it fires:
        \Log::info('TIMESHEET SAVE HIT', [
            'date' => $this->date,
            'project_id' => $this->project_id,
            'time_input' => $this->time_input,
        ]);

        if ($this->isLocked) {
            $this->addError('date', 'This month is locked.');
            return;
        }

        $this->validate([
            'date' => 'required|date',
            'project_id' => 'required|exists:projects,id',
            'time_input' => 'required',
            'description' => 'nullable|string|max:2000',
        ]);

        try {
            $parsed = TimeParser::parse($this->time_input);
        } catch (\Throwable $e) {
            // if parsing fails, show it under the time field
            $this->addError('time_input', $e->getMessage());
            return;
        }

        // be tolerant about parser output shape
        $minutes = $parsed['rounded_minutes']
            ?? $parsed['minutes']
            ?? null;

        if ($minutes === null) {
            $this->addError('time_input', 'Could not parse time input.');
            return;
        }

        TimeEntry::updateOrCreate(
            ['id' => $this->editingId],
            [
                'user_id' => Auth::id(),
                'project_id' => $this->project_id,
                'date' => $this->date,
                'minutes' => $minutes,
                'description' => $this->description,
            ]
        );

        $this->loadEntries();

        // optional: clear the form so next open is clean
        $this->reset(['editingId','date','project_id','time_input','description','parsedPreview','errorPreview']);
        $this->resetValidation();

        $this->showModal = false;   // ✅ Livewire-controlled close
    }

    public function saveEntry()
    {
        \Log::info('TIMESHEET saveEntry HIT', [
            'date' => $this->date,
            'project_id' => $this->project_id,
            'time_input' => $this->time_input,
        ]);

        // call your existing logic
        return $this->save();
    }

    public function delete($id)
    {
        if ($this->isLocked) return;

        TimeEntry::where('id', $id)->where('user_id', Auth::id())->delete();
        $this->loadEntries();
    }

    public function render()
    {
        return view('livewire.timesheet', [
            'entriesByDate' => $this->entriesByDate,
            'totalMinutes' => $this->totalMinutes,
            'totalHours' => $this->totalHours,
            'isLocked' => $this->isLocked,
        ]);
    }
}
