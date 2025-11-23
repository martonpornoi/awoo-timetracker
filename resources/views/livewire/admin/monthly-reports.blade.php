<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Monthly Reports</h1>

        <input type="month"
               wire:model.live="month"
               class="border rounded px-3 py-1" />
    </div>

    {{-- If report exists --}}
    @if($report)
        <div class="bg-gray-50 border rounded p-4 space-y-4">

            <div class="flex items-center justify-between">
                <div>
                    <div class="font-semibold text-lg">
                        Report for {{ \Carbon\Carbon::parse($report->month)->format('F Y') }}
                    </div>
                    <div class="text-sm text-gray-600">
                        Status:
                        @if($report->status === 'closed')
                            <span class="text-red-600 font-semibold">Closed</span>
                        @else
                            <span class="text-green-700 font-semibold">Open</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3">
                    <button wire:click="generate"
                            class="px-3 py-2 bg-blue-600 text-white rounded">
                        Re-generate
                    </button>

                    @if($report->status === 'open')
                        <button wire:click="close"
                                class="px-3 py-2 bg-red-600 text-white rounded">
                            Close Month
                        </button>
                    @else
                        <button wire:click="reopen"
                                class="px-3 py-2 bg-yellow-600 text-white rounded">
                            Reopen
                        </button>
                    @endif
                </div>
            </div>

            {{-- TOTAL --}}
            <div class="text-lg">
                <b>Total:</b> {{ round(($snapshot['total_minutes'] ?? 0) / 60, 2) }} h
            </div>

            @if(!empty($snapshot))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- By User --}}
                    <div class="border rounded p-4">
                        <h2 class="font-semibold mb-3">By User</h2>

                        <ul class="space-y-2">
                            @foreach($snapshot['by_user'] ?? [] as $uid => $row)
                                @php
                                    $user = \App\Models\User::find($uid);
                                @endphp
                                <li>
                                    <b>{{ $user?->name ?? 'User '.$uid }}</b> —
                                    {{ round($row['minutes']/60,2) }} h
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- By Project --}}
                    <div class="border rounded p-4">
                        <h2 class="font-semibold mb-3">By Project</h2>

                        <ul class="space-y-2">
                            @foreach($snapshot['by_project'] ?? [] as $pid => $row)
                                @php
                                    $p = \App\Models\Project::find($pid);
                                @endphp
                                <li>
                                    <b>{{ $p?->name ?? 'Project '.$pid }}</b> —
                                    {{ round($row['minutes']/60,2) }} h
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            @endif

        </div>

    @else
        {{-- No report --}}
        <div class="bg-yellow-50 border rounded p-4 text-yellow-800 space-y-4">
            <div>No report exists for this month.</div>

            <button wire:click="generate"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
                Generate Report
            </button>
        </div>
    @endif

</div>
