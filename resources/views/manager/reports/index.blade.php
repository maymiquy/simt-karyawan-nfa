@extends('layouts.manager')

@section('title', 'Laporan')
@section('page-title', 'Laporan Tugas')
@section('page-subtitle', 'Unduh dan analisis data tugas')

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <x-stats-card label="Total Tugas"         :value="$totalTasks"     color="blue"/>
    <x-stats-card label="Selesai Tepat Waktu" :value="$completedOnTime" color="green"/>
    <x-stats-card label="Terlambat"           :value="$overdueTasks"    :color="$overdueTasks>0?'red':'gray'"/>
    <x-stats-card label="Sedang Berjalan"     :value="$inProgressTasks" color="amber"/>
</div>

{{-- Filter + Download --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-5 transition-colors duration-200">
    <form method="GET" action="{{ route('manager.reports.index') }}" id="filterForm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            @foreach([['from','Dari Tanggal','date'],['to','Sampai Tanggal','date']] as [$n,$l,$t])
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">{{ $l }}</label>
                <input type="{{ $t }}" name="{{ $n }}" value="{{ request($n) }}"
                       class="block w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
            </div>
            @endforeach
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                <select name="status"
                        class="block w-full px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                    <option value="">Semua Status</option>
                    @foreach(['pending'=>'Pending','in_progress'=>'Proses','completed'=>'Selesai','overdue'=>'Terlambat','cancelled'=>'Dibatalkan'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Karyawan</label>
                <select name="employee_id"
                        class="block w-full px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
                    <option value="">Semua Karyawan</option>
                    @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id?'selected':'' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit"
                    class="px-4 py-2.5 text-sm font-medium text-white rounded-xl"
                    style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">Terapkan Filter</button>

            @if(request()->hasAny(['from','to','status','employee_id']))
            <a href="{{ route('manager.reports.index') }}"
               class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Reset
            </a>
            @endif

            <div class="flex-1"></div>

            <a href="{{ route('manager.reports.pdf', request()->only(['from','to','status','employee_id'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('manager.reports.excel', request()->only(['from','to','status','employee_id'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Excel
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
@if ($tasks->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
        <x-empty-state title="Tidak ada data yang sesuai filter."/>
    </div>
@else
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-200">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50/70 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                <tr>
                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 px-5 py-3">Tugas</th>
                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-3 hidden md:table-cell">Prioritas</th>
                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-3 hidden sm:table-cell">Assignee</th>
                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-3 hidden lg:table-cell">Tenggat</th>
                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-3">Status</th>
                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 px-3 py-3 hidden xl:table-cell">Dibuat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach ($tasks as $task)
                <tr class="hover:bg-gray-50/40 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-5 py-3">
                        <a href="{{ route('manager.tasks.show', $task->id) }}"
                           class="font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            {{ Str::limit($task->title, 50) }}
                        </a>
                    </td>
                    <td class="px-3 py-3 hidden md:table-cell"><x-priority-badge :priority="$task->priority"/></td>
                    <td class="px-3 py-3 hidden sm:table-cell text-xs text-gray-500 dark:text-gray-400">
                        {{ $task->assignments->pluck('user.name')->filter()->implode(', ') ?: '—' }}
                    </td>
                    <td class="px-3 py-3 hidden lg:table-cell text-xs text-gray-500 dark:text-gray-400">
                        {{ $task->due_date?->translatedFormat('d M Y') ?? '—' }}
                    </td>
                    <td class="px-3 py-3"><x-status-badge :status="$task->status"/></td>
                    <td class="px-3 py-3 hidden xl:table-cell text-xs text-gray-400 dark:text-gray-500">
                        {{ $task->created_at->translatedFormat('d M Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($tasks->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">{{ $tasks->links() }}</div>
    @endif
</div>
@endif

@endsection
