<div>
    <div class="p-6 space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Project Manager</h1>

            <button wire:click="openCreateModal"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                Add Project
            </button>
        </div>

        {{-- Project list --}}
        <div class="border rounded-lg overflow-hidden bg-white">
            <table class="min-w-full w-full">
                <thead class="bg-gray-50 border-b">
                <tr class="text-left text-sm text-gray-600">
                    <th class="p-2">Name</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Entries</th>
                    <th class="p-2">Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($projects as $p)
                    {{-- Project row --}}
                    <tr class="border-b align-top">
                        <td class="p-2 font-semibold">
                            {{ $p->name }}
                            @if($p->code)
                                <div class="text-xs text-gray-400">{{ $p->code }}</div>
                            @endif
                        </td>

                        <td class="p-2">
                            @if($p->is_active)
                                <span class="text-green-700 font-semibold">Active</span>
                            @else
                                <span class="text-gray-500 font-medium">Archived</span>
                            @endif
                        </td>

                        <td class="p-2 text-sm text-gray-700">
                            {{ $p->time_entries_count }}
                        </td>

                        <td class="p-2">
                            <button wire:click="edit({{ $p->id }})"
                                    class="text-blue-600 mr-2">
                                Edit
                            </button>

                            <button wire:click="toggleActive({{ $p->id }})"
                                    class="text-yellow-600">
                                {{ $p->is_active ? 'Archive' : 'Activate' }}
                            </button>
                        </td>
                    </tr>

                    {{-- Time entries row --}}
                    <tr class="border-b bg-gray-50/40">
                        <td colspan="4" class="p-0">
                            <details class="group">
                                <summary class="cursor-pointer select-none px-4 py-2 flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-700">
                                        Time entries ({{ $p->time_entries_count }})
                                    </span>
                                    <span class="text-gray-500 group-open:hidden">show</span>
                                    <span class="text-gray-500 hidden group-open:inline">hide</span>
                                </summary>

                                <div class="px-4 pb-3">
                                    @if($p->timeEntries->isEmpty())
                                        <div class="text-sm text-gray-500 py-3">
                                            No entries for this project yet.
                                        </div>
                                    @else
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                <tr class="text-left text-gray-500 border-b">
                                                    <th class="py-2 pr-3">Date</th>
                                                    <th class="py-2 pr-3">User</th>
                                                    <th class="py-2 pr-3">Hours</th>
                                                    <th class="py-2 pr-3">Description</th>
                                                    <th class="py-2 pr-3">Locked</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($p->timeEntries as $e)
                                                    <tr class="border-b last:border-0">
                                                        <td class="py-2 pr-3 whitespace-nowrap">
                                                            {{ $e->date->format('Y-m-d') }}
                                                        </td>
                                                        <td class="py-2 pr-3 whitespace-nowrap">
                                                            {{ $e->user?->name ?? '—' }}
                                                        </td>
                                                        <td class="py-2 pr-3 whitespace-nowrap">
                                                            {{ number_format($e->hours, 2) }}
                                                        </td>
                                                        <td class="py-2 pr-3">
                                                            {{ $e->description ?: '—' }}
                                                        </td>
                                                        <td class="py-2 pr-3 whitespace-nowrap">
                                                            @if($e->locked_by_report_id)
                                                                <span class="text-xs font-semibold text-red-700">
                                                                    Yes
                                                                </span>
                                                            @else
                                                                <span class="text-xs text-gray-500">
                                                                    No
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            No projects yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg w-[26rem]">

                <h2 class="text-lg font-bold mb-4">
                    {{ $editingId ? 'Edit Project' : 'Add Project' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold">Name</label>
                        <input type="text"
                               wire:model.defer="name"
                               class="border rounded w-full p-2">
                        @error('name')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.defer="is_active">
                        <label>Active</label>
                    </div>

                    <div class="flex justify-end pt-4 gap-3">
                        <button type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 border rounded">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded">
                            Save
                        </button>
                    </div>

                    <div wire:loading class="text-sm text-gray-500">
                        Saving...
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
