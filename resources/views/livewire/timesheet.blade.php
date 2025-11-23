<div class="p-6 space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold">My Timesheet</h1>
            <div class="text-sm text-gray-600">
                Total this month: <b>{{ $totalHours }} h</b> ({{ $totalMinutes }} min)
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input type="month"
                   wire:model.live="month"
                   class="border rounded px-2 py-1" />

            @if($isLocked)
                <span class="text-red-600 font-semibold">Month locked</span>
            @else
                <button wire:click="openCreateModal"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                    Add Entry
                </button>
            @endif
        </div>
    </div>

    {{-- Grouped by day --}}
    <div class="space-y-4">
        @forelse($entriesByDate as $day => $rows)
            @php $dayMinutes = $rows->sum('minutes'); @endphp

            <div class="border rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 flex justify-between items-center">
                    <div class="font-semibold">
                        {{ $day }}
                        <span class="text-gray-500 font-normal">
                            ({{ round($dayMinutes/60,2) }} h)
                        </span>
                    </div>

                    @if(!$isLocked)
                        <button wire:click="openCreateModal('{{ $day }}')"
                                class="text-blue-600 text-sm font-semibold">
                            + add for this day
                        </button>
                    @endif
                </div>

                <table class="min-w-full w-full">
                    <thead>
                    <tr class="border-b text-sm text-gray-600">
                        <th class="p-2 text-left">Project</th>
                        <th class="p-2 text-left">Time</th>
                        <th class="p-2 text-left">Description</th>
                        <th class="p-2"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $e)
                        <tr class="border-b">
                            <td class="p-2">{{ $e->project->name }}</td>
                            <td class="p-2">{{ round($e->minutes/60,2) }} h</td>
                            <td class="p-2">{{ $e->description }}</td>
                            <td class="p-2 text-right">
                                @if(!$isLocked)
                                    <button wire:click="editEntry({{ $e->id }})"
                                            class="text-blue-600 mr-2">Edit</button>
                                    <button wire:click="delete({{ $e->id }})"
                                            class="text-red-600">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-gray-500">No entries yet for this month.</div>
        @endforelse
    </div>

    {{-- Modal --}}
@if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[28rem] space-y-4">

            <h2 class="text-lg font-bold">
                {{ $editingId ? 'Edit Entry' : 'Add Entry' }}
            </h2>

            <div>
                <label class="block text-sm font-medium">Date</label>
                <input type="date" wire:model.live="date"
                       class="border rounded w-full p-2">
                @error('date') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Project</label>
                <select wire:model.live="project_id"
                        class="border rounded w-full p-2">
                    <option value="">Select...</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('project_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Time</label>
                <input type="text"
                       wire:model.live.debounce.400ms="time_input"
                       class="border rounded w-full p-2"
                       placeholder="e.g. 2h15m / 1:30 / 2.25">

                @if($parsedPreview)
                    <div class="text-green-700 text-sm mt-1">{{ $parsedPreview }}</div>
                @elseif($errorPreview)
                    <div class="text-red-600 text-sm mt-1">{{ $errorPreview }}</div>
                @endif

                @error('time_input') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea wire:model.live="description"
                          class="border rounded w-full p-2"
                          rows="3"></textarea>
                @error('description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button"
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 border rounded">
                    Cancel
                </button>

                <button type="button"
                        wire:click="save"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                    Save
                </button>
            </div>
        </div>
    </div>
@endif

</div>
