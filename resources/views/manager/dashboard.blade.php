@extends('layouts.manager')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas tugas')

@section('header-actions')
<a href="{{ route('manager.tasks.create') }}"
   class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white rounded-xl transition-all"
   style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Buat Tugas
</a>
@endsection

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-500">Total Tugas</p>
            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $totalTasks }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-500">Sedang Berjalan</p>
            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $inProgressTasks }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-500">Selesai Bulan Ini</p>
            <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $completedMonth }}</p>
    </div>

    <a href="{{ route('manager.tasks.index', ['status' => 'overdue']) }}"
       class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:border-red-200 hover:shadow-md transition-all group">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-500">Terlambat</p>
            <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold {{ $overdueTasks > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $overdueTasks }}</p>
        @if ($overdueTasks > 0)
        <p class="text-xs text-red-400 mt-0.5 group-hover:underline">Lihat detail →</p>
        @endif
    </a>
</div>

{{-- Chart + Active Tasks --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- Bar Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Aktivitas 7 Hari Terakhir</h2>
        <div class="relative h-48">
            <canvas id="taskChart"></canvas>
        </div>
        <div class="flex items-center justify-center gap-5 mt-3">
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-3 h-3 rounded-sm" style="background:#22c55e"></span> Selesai
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-3 h-3 rounded-sm" style="background:#ef4444"></span> Terlambat
            </div>
        </div>
    </div>

    {{-- Active Tasks Table --}}
    <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700">Tugas Aktif</h2>
            <a href="{{ route('manager.tasks.index') }}"
               class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                Lihat semua →
            </a>
        </div>

        @if ($activeTasks->isEmpty())
            <div class="flex flex-col items-center py-10 text-center">
                <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                </svg>
                <p class="text-gray-400 text-sm">Tidak ada tugas aktif</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-medium text-gray-400 pb-2.5">Tugas</th>
                            <th class="text-left text-xs font-medium text-gray-400 pb-2.5 hidden sm:table-cell">Assignee</th>
                            <th class="text-left text-xs font-medium text-gray-400 pb-2.5 hidden md:table-cell">Tenggat</th>
                            <th class="text-right text-xs font-medium text-gray-400 pb-2.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($activeTasks as $task)
                        @php
                            $isOverdue = $task->due_date && $task->due_date->isPast() && !in_array($task->status, ['completed','cancelled']);
                        @endphp
                        <tr class="{{ $isOverdue ? 'bg-red-50/50' : '' }}">
                            <td class="py-2.5 pr-3">
                                <div class="flex items-center gap-2">
                                    @if ($task->priority === 'high')
                                        <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                    @elseif ($task->priority === 'medium')
                                        <span class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
                                    @endif
                                    <span class="font-medium text-gray-800 truncate max-w-[150px]">{{ $task->title }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 pr-3 hidden sm:table-cell">
                                <div class="flex -space-x-1">
                                    @foreach ($task->assignments->take(3) as $a)
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white ring-2 ring-white"
                                         style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)"
                                         title="{{ $a->user?->name }}">
                                        {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if ($task->assignments->count() > 3)
                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 ring-2 ring-white">
                                            +{{ $task->assignments->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 pr-3 hidden md:table-cell">
                                @if ($task->due_date)
                                    <span class="text-xs {{ $isOverdue ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                        {{ $task->due_date->translatedFormat('d M') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 text-right">
                                <a href="{{ route('manager.tasks.show', $task->id) }}"
                                   class="text-xs text-blue-600 hover:text-blue-700 font-medium">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- FAB mobile --}}
<a href="{{ route('manager.tasks.create') }}"
   class="fixed bottom-6 right-6 sm:hidden w-14 h-14 rounded-full flex items-center justify-center shadow-lg text-white z-10"
   style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
</a>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('taskChart');
    if (ctx) {
        const chartData = @json($chartData);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [
                    {
                        label: 'Selesai',
                        data: chartData.map(d => d.completed),
                        backgroundColor: 'rgba(34,197,94,0.7)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Terlambat',
                        data: chartData.map(d => d.overdue),
                        backgroundColor: 'rgba(239,68,68,0.7)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                }
            }
        });
    }
</script>
@endpush
