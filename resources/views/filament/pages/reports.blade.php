<x-filament-panels::page>

    {{-- Filter Form --}}
    <x-filament::section>
        <x-slot name="heading">Filter Laporan</x-slot>

        <form wire:submit="filter">
            {{ $this->form }}

            <div class="mt-4 flex gap-3">
                <x-filament::button type="submit" color="primary">
                    Terapkan Filter
                </x-filament::button>
                <x-filament::button
                    wire:click="$set('from', null); $set('to', null); $set('status', null); $set('employee_id', null)"
                    color="gray"
                    outlined>
                    Reset
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    {{-- Summary Stats --}}
    @php $summary = $this->getSummary(); @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-filament::section compact>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Tugas</p>
            </div>
        </x-filament::section>
        <x-filament::section compact>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $summary['completed'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Selesai</p>
            </div>
        </x-filament::section>
        <x-filament::section compact>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">{{ $summary['overdue'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Terlambat</p>
            </div>
        </x-filament::section>
        <x-filament::section compact>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $summary['in_progress'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Sedang Proses</p>
            </div>
        </x-filament::section>
    </div>

    {{-- Data Table --}}
    <x-filament::section>
        <x-slot name="heading">Preview Data</x-slot>

        @php $tasks = $this->getTasks(); @endphp

        @if ($tasks->isEmpty())
            <div class="py-10 text-center text-gray-400 text-sm">
                Tidak ada data yang sesuai filter.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left text-xs font-medium text-gray-500 pb-3 pr-4">Judul</th>
                            <th class="text-left text-xs font-medium text-gray-500 pb-3 pr-4 hidden md:table-cell">Prioritas</th>
                            <th class="text-left text-xs font-medium text-gray-500 pb-3 pr-4 hidden sm:table-cell">Assignee</th>
                            <th class="text-left text-xs font-medium text-gray-500 pb-3 pr-4 hidden lg:table-cell">Tenggat</th>
                            <th class="text-left text-xs font-medium text-gray-500 pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($tasks as $task)
                        @php
                            $statusColors = ['pending'=>'gray','in_progress'=>'blue','completed'=>'green','overdue'=>'red','cancelled'=>'gray'];
                            $statusLabels = ['pending'=>'Pending','in_progress'=>'Proses','completed'=>'Selesai','overdue'=>'Terlambat','cancelled'=>'Dibatalkan'];
                            $priorityColors = ['high'=>'red','medium'=>'amber','low'=>'green'];
                            $priorityLabels = ['high'=>'Tinggi','medium'=>'Sedang','low'=>'Rendah'];
                        @endphp
                        <tr class="py-2">
                            <td class="py-2.5 pr-4 font-medium text-gray-800 dark:text-white">
                                {{ Str::limit($task->title, 45) }}
                            </td>
                            <td class="py-2.5 pr-4 hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    bg-{{ $priorityColors[$task->priority] ?? 'gray' }}-50
                                    text-{{ $priorityColors[$task->priority] ?? 'gray' }}-700">
                                    {{ $priorityLabels[$task->priority] ?? $task->priority }}
                                </span>
                            </td>
                            <td class="py-2.5 pr-4 hidden sm:table-cell text-xs text-gray-500">
                                {{ $task->assignments->pluck('user.name')->filter()->implode(', ') ?: '—' }}
                            </td>
                            <td class="py-2.5 pr-4 hidden lg:table-cell text-xs text-gray-500">
                                {{ $task->due_date?->translatedFormat('d M Y') ?? '—' }}
                            </td>
                            <td class="py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    bg-{{ $statusColors[$task->status] ?? 'gray' }}-50
                                    text-{{ $statusColors[$task->status] ?? 'gray' }}-700">
                                    {{ $statusLabels[$task->status] ?? $task->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        @endif
    </x-filament::section>

</x-filament-panels::page>
