<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use Illuminate\Support\Str;
use Livewire\Component;

class Projects extends Component
{
    public ?int $editingId = null;
    public string $name = '';
    public bool $is_active = true;

    public bool $showModal = false; // <-- ADD THIS

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'name', 'is_active']);
        $this->is_active = true;
        $this->showModal = true;     // <-- OPEN VIA LIVEWIRE
    }

    public function edit(int $id): void
    {
        $p = Project::findOrFail($id);

        $this->editingId = $p->id;
        $this->name = $p->name;
        $this->is_active = (bool) $p->is_active;

        $this->showModal = true;     // <-- OPEN VIA LIVEWIRE
    }

    public function closeModal(): void
    {
        $this->showModal = false;    // <-- CLOSE VIA LIVEWIRE
    }

    public function save(): void
    {
        // quick proof it fires:
        \Log::info('SAVE HIT', ['name' => $this->name]);

        $this->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        if ($this->editingId === null) {
            Project::create([
                'name' => $this->name,
                'code' => Str::slug($this->name) . '-' . Str::random(4),
                'is_active' => $this->is_active,
                'owner_id' => auth()->id(),
            ]);
        } else {
            Project::whereKey($this->editingId)->update([
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
        }

        $this->reset(['editingId', 'name', 'is_active']);
        $this->showModal = false;    // <-- CLOSE AFTER SAVE
    }

    public function toggleActive(int $id): void
    {
        $p = Project::findOrFail($id);
        $p->update(['is_active' => !$p->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.projects', [
            'projects' => Project::query()
                ->with([
                    'timeEntries' => function ($q) {
                        $q->latest('date')
                        ->latest('id')
                        ->with('user'); // show who logged it
                    }
                ])
                ->withCount('timeEntries')
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
        ]);
    }

}
