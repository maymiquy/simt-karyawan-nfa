@extends('layouts.manager')

@section('title', 'Laporan')
@section('page-title', 'Laporan Tugas')
@section('page-subtitle', 'Unduh dan analisis data tugas')

@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 mb-1">Total Tugas</p>
        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 mb-1">Selesai Tepat Waktu</p>
        <p class="text-2xl font-bold text-green-600">{{ $completedOnTime }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 mb-1">Terlambat</p>
        <p class="text-2xl font-bold text-red-600">{{ $overdueTasks }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 mb-1">Sedang Berjalan</p>
        <p class="text-2xl font-bold text-blue-600">{{ $inProgressTasks }}</p>
    </div>
</div>

{{-- Filter + Download --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <form method="GET" action="{{ route('manager.reports.index') }}" id="filterForm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="block w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="block w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                <select name="status"
                        class="block w-full px-3 py-2.5 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
                    <option value="">Semua Status</option>
                    @foreach(['pending'=>'Pending','in_progress'=>'Proses','completed'=>'Selesai','overdue'=>'Terlambat','cancelled'=>'Dibatalkan'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Karyawan</label>
                <select name="employee_id"
                        class="block w-full px-3 py-2.5 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 transition-colors">
                    <option value="">Semua Karyawan</option>
                    @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit"
                    class="px-4 py-2.5 text-sm font-medium text-white rounded-xl"
                    style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
                Terapkan Filter
            </button>

            @if (request()->hasAny(['from','to','status','employee_id']))
            <a href="{{ route('manager.reports.index') }}"
               class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                Reset
            </a>
            @endif

            <div class="flex-1"></div>

            {{-- Download buttons --}}
            <a href="{{ route('manager.reports.pdf', request()->only(['from','to','status','employee_id'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-700 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('manager.reports.excel', request()->only(['from','to','status','employee_id'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-green-700 bg-green-50 border border-green-100 rounded-xl hover:bg-green-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Excel
            </a>
        </div>
    </form>
</div>

{{-- Table preview --}}
@if ($tasks->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
        <p class="text-gray-400">Tidak ada data yang sesuai filter.</p>
    </div>
@else
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50/70 border-b border-gray-100">
                <tr>
                    <th class="text-left text-xs font-medium text-gray-500 px-5 py-3">Tugas</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden md:table-cell">Prioritas</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden sm:table-cell">Assignee</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden lg:table-cell">Tenggat</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3">Status</th>
                    <th class="text-left text-xs font-medium text-gray-500 px-3 py-3 hidden xl:table-cell">Dibuat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($tasks as $task)
                <tr class="hover:bg-gray-50/40">
                    <td class="px-5 py-3">
                        <a href="{{ route('manager.tasks.show', $task->id) }}"
                           class="font-medium text-gray-800 hover:text-blue-600 transition-colors">
                            {{ Str::limit($task->title, 50) }}
                        </a>
                    </td>
                    <td class="px-3 py-3 hidden md:table-cell">
                        <x-priority-badge :priority="$task->priority"/>
                    </td>
                    <td class="px-3 py-3 hidden sm:table-cell text-xs text-gray-500">
                        {{ $task->assignments->pluck('user.name')->filter()->implode(', ') ?: '—' }}
                    </td>
                    <td class="px-3 py-3 hidden lg:table-cell text-xs text-gray-500">
                        {{ $task->due_date?->translatedFormat('d M Y') ?? '—' }}
                    </td>
                    <td class="px-3 py-3">
                        <x-status-badge :status="$task->status"/>
                    </td>
                    <td class="px-3 py-3 hidden xl:table-cell text-xs text-gray-400">
                        {{ $task->created_at->translatedFormat('d M Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($tasks->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endif

@endsection
