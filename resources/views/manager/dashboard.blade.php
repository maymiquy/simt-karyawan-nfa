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

    <x-stats-card
        label="Total Tugas"
        :value="$totalTasks"
        color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
    />

    <x-stats-card
        label="Sedang Berjalan"
        :value="$inProgressTasks"
        color="amber"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
    />

    <x-stats-card
        label="Selesai Bulan Ini"
        :value="$completedMonth"
        color="green"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />

    <x-stats-card
        label="Terlambat"
        :value="$overdueTasks"
        :color="$overdueTasks > 0 ? 'red' : 'gray'"
        :description="$overdueTasks > 0 ? 'Klik untuk lihat detail →' : 'Tepat waktu'"
        :href="$overdueTasks > 0 ? route('manager.tasks.index', ['status' => 'overdue']) : null"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    />
</div>

{{-- Chart + Active Tasks --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- Bar Chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Aktivitas 7 Hari Terakhir</h2>
        <div class="relative h-48">
            <canvas id="taskChart"></canvas>
        </div>
        <div class="flex items-center justify-center gap-5 mt-3">
            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                <span class="w-3 h-3 rounded-sm inline-block" style="background:#22c55e"></span> Selesai
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                <span class="w-3 h-3 rounded-sm inline-block" style="background:#ef4444"></span> Terlambat
            </div>
        </div>
    </div>

    {{-- Active Tasks Table --}}
    <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tugas Aktif</h2>
            <a href="{{ route('manager.tasks.index') }}"
               class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium transition-colors">
                Lihat semua →
            </a>
        </div>

        @if ($activeTasks->isEmpty())
            <x-empty-state
                title="Tidak ada tugas aktif"
                description="Semua tugas sudah selesai atau belum ada tugas dibuat."
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5">Tugas</th>
                            <th class="text-left text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5 hidden sm:table-cell">Assignee</th>
                            <th class="text-left text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5 hidden md:table-cell">Tenggat</th>
                            <th class="text-right text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach ($activeTasks as $task)
                        @php $isOverdue = $task->due_date && $task->due_date->isPast() && !in_array($task->status, ['completed','cancelled']); @endphp
                        <tr class="{{ $isOverdue ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                            <td class="py-2.5 pr-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full shrink-0
                                        {{ $task->priority === 'high' ? 'bg-red-500' : ($task->priority === 'medium' ? 'bg-amber-400' : 'bg-green-400') }}">
                                    </span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[150px]">{{ $task->title }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 pr-3 hidden sm:table-cell">
                                <div class="flex -space-x-1">
                                    @foreach ($task->assignments->take(3) as $a)
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white ring-2 ring-white dark:ring-gray-800"
                                         style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)"
                                         title="{{ $a->user?->name }}">
                                        {{ strtoupper(substr($a->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if ($task->assignments->count() > 3)
                                        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs text-gray-600 dark:text-gray-300 ring-2 ring-white dark:ring-gray-800">
                                            +{{ $task->assignments->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2.5 pr-3 hidden md:table-cell">
                                @if ($task->due_date)
                                    <span class="text-xs {{ $isOverdue ? 'text-red-500 dark:text-red-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $task->due_date->translatedFormat('d M') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 text-right">
                                <a href="{{ route('manager.tasks.show', $task->id) }}"
                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium transition-colors">Detail</a>
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
{{-- Feed Aktivitas Tim --}}
<div class="mt-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Aktivitas Tim Terbaru</h2>
        <a href="{{ route('manager.activity.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Lihat semua →</a>
    </div>
    @if ($recentLogs->isEmpty())
        <x-empty-state title="Belum ada aktivitas" description="Aktivitas karyawan akan muncul di sini." />
    @else
        <div class="space-y-3">
            @foreach ($recentLogs as $log)
            @php $p = $log->presentation();
                 $dot = ['gray'=>'bg-gray-300 dark:bg-gray-600','blue'=>'bg-blue-500','indigo'=>'bg-indigo-500','amber'=>'bg-amber-400','green'=>'bg-green-500'][$p['color']] ?? 'bg-gray-300';
            @endphp
            <div class="flex items-start gap-3">
                <span class="mt-1.5 w-2.5 h-2.5 rounded-full shrink-0 {{ $dot }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        <span class="font-medium">{{ $log->assignment?->user?->name ?? 'Karyawan' }}</span>
                        <span class="text-gray-500 dark:text-gray-400">— {{ strtolower($p['label']) }}</span>
                        <span class="text-gray-400 dark:text-gray-500">·</span>
                        <a href="{{ route('manager.tasks.show', $log->assignment?->task_id) }}"
                           class="text-blue-600 dark:text-blue-400 hover:underline">{{ $log->assignment?->task?->title ?? 'Tugas' }}</a>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                        @if($log->type === 'approved' && isset($log->meta['kpi'])) · <span class="text-green-600 dark:text-green-400 font-medium">KPI {{ $log->meta['kpi'] }}</span>@endif
                    </p>
                    @if($log->notes)
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-2.5 py-1.5">{{ $log->notes }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

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
