<div>
    {{-- Debug/ping --}}
    <div class="mb-4 p-2 border rounded">
        Ping counter: {{ $pingCount }}
        <button wire:click="ping" class="ml-2 px-2 py-1 border rounded">
            Ping
        </button>
    </div>

    <div class="p-6 space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Project Manager</h1>

            <button wire:click="openCreateModal"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                Add Project
            </button>
        </div>

        {{-- Project list --}}
        <div class="border rounded-lg overflow-hidden">
            <table class="min-w-full w-full">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-sm text-gray-600">
                        <th class="p-2">Name</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($projects as $p)
                        <tr class="border-b">
                            <td class="p-2">{{ $p->name }}</td>

                            <td class="p-2">
                                @if($p->is_active)
                                    <span class="text-green-700 font-semibold">Active</span>
                                @else
                                    <span class="text-gray-500 font-medium">Archived</span>
                                @endif
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
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500">
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
