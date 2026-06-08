@extends('layouts.employee')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stats + KPI --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stats-card label="Tugas Aktif" :value="$activeCount" description="Sedang berjalan" color="blue"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>'/>
    <x-stats-card label="Disetujui" :value="$doneCount" description="Tugas selesai" color="green"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'/>
    <x-stats-card label="Tenggat Dekat" :value="$nearDueCount" description="Dalam 3 hari" color="amber"
        icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'/>

    {{-- KPI Card (v2 composite) --}}
    @php
        $kpiColor = \App\Services\KpiService::color($kpi['percent']);
        $kpiText  = ['green'=>'text-green-600 dark:text-green-400','amber'=>'text-amber-500 dark:text-amber-400','red'=>'text-red-500 dark:text-red-400','gray'=>'text-gray-400'][$kpiColor];
        $kpiBg    = ['green'=>'bg-green-50 dark:bg-green-900/20','amber'=>'bg-amber-50 dark:bg-amber-900/20','red'=>'bg-red-50 dark:bg-red-900/20','gray'=>'bg-gray-100 dark:bg-gray-700'][$kpiColor];
        $delta    = $kpi['trend']['delta'] ?? null;
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">KPI Saya</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $kpi['period_label'] }}</p>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $kpiBg }}">
                <svg class="w-5 h-5 {{ $kpiText }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <div class="flex items-end gap-2">
            <p class="text-3xl font-bold {{ $kpiText }}">{{ $kpi['percent'] === null ? '—' : $kpi['percent'].'%' }}</p>
            @if($delta !== null && $delta !== 0)
                <span class="mb-1 text-xs font-medium {{ $delta > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                    {{ $delta > 0 ? '▲' : '▼' }}{{ abs($delta) }}%
                </span>
            @endif
        </div>
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs font-medium {{ $kpiText }}">{{ $kpi['band_label'] }}</span>
            <span class="text-[11px] text-gray-400 dark:text-gray-500">Target {{ $kpi['target'] }}%</span>
        </div>
    </div>
</div>

{{-- Rincian KPI (3 dimensi) --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-4 transition-colors duration-200">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Rincian KPI — {{ $kpi['period_label'] }}</h2>
        <span class="text-xs text-gray-400 dark:text-gray-500">
            {{ $kpi['approved'] }}/{{ $kpi['due_in_period'] }} tugas disetujui ·
            {{ $kpi['on_time'] }} tepat waktu · {{ $kpi['total_revisions'] }} revisi
        </span>
    </div>

    @if ($kpi['percent'] === null)
        <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada tugas yang jatuh tempo pada bulan ini.</p>
    @else
        <div class="space-y-3">
            @foreach([
                ['Quality (mutu, 50%)', $kpi['breakdown']['quality'], 'bg-blue-500'],
                ['On-Time (ketepatan waktu, 30%)', $kpi['breakdown']['ontime'], 'bg-green-500'],
                ['Completion (penyelesaian, 20%)', $kpi['breakdown']['completion'], 'bg-amber-500'],
            ] as [$label, $val, $bar])
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600 dark:text-gray-300">{{ $label }}</span>
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $val !== null ? rtrim(rtrim(number_format($val,1),'0'),'.').'%' : '—' }}</span>
                </div>
                <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full rounded-full {{ $bar }}" style="width: {{ $val !== null ? min(100,$val) : 0 }}%; transition: width .5s ease"></div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">
            KPI = 50% × Quality + 30% × On-Time + 20% × Completion
        </p>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Donut progress --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Progres Keseluruhan</h2>
        <div class="flex items-center justify-center mb-4">
            <div class="relative w-28 h-28">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="38" fill="none" class="stroke-gray-100 dark:stroke-gray-700" stroke-width="10"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#3b82f6" stroke-width="10"
                            stroke-linecap="round" stroke-dasharray="{{ round($completionRate * 2.39) }} 239"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completionRate }}%</span>
                </div>
            </div>
        </div>
        <div class="space-y-2 text-sm">
            @foreach([['Disetujui','bg-green-500',$doneCount],['Aktif','bg-blue-500',$activeCount],['Total','bg-gray-300 dark:bg-gray-600',$totalCount]] as [$lbl,$dot,$val])
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $dot }}"></span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $lbl }}</span>
                </div>
                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tugas Terbaru --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tugas Terbaru</h2>
            <a href="{{ route('employee.tasks.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Lihat semua →</a>
        </div>
        @if ($recentAssignments->isEmpty())
            <x-empty-state title="Belum ada tugas" description="Belum ada tugas yang diberikan kepada Anda."/>
        @else
            <div class="space-y-1">
                @foreach ($recentAssignments as $assignment)
                <a href="{{ route('employee.tasks.show', $assignment->id) }}"
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                    <div class="w-2 h-2 rounded-full shrink-0 mt-0.5
                        {{ $assignment->task?->priority === 'high' ? 'bg-red-500' : ($assignment->task?->priority === 'medium' ? 'bg-amber-400' : 'bg-green-400') }}">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $assignment->task?->title ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ $assignment->task?->due_date ? 'Tenggat: '.$assignment->task->due_date->translatedFormat('d M Y, H:i') : 'Tanpa tenggat' }}
                        </p>
                    </div>
                    <x-status-badge :status="$assignment->progress" class="shrink-0"/>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Aktivitas Saya (feed) --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Aktivitas Saya Terbaru</h2>
    @if ($recentLogs->isEmpty())
        <x-empty-state title="Belum ada aktivitas" description="Mulai kerjakan tugas untuk melihat jejak aktivitas." />
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
                        <span class="font-medium">{{ $p['label'] }}</span>
                        <span class="text-gray-400 dark:text-gray-500">·</span>
                        <a href="{{ route('employee.tasks.show', $log->assignment_id) }}"
                           class="text-blue-600 dark:text-blue-400 hover:underline">{{ $log->assignment?->task?->title ?? 'Tugas' }}</a>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</p>
                    @if($log->notes)
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-2.5 py-1.5">{{ $log->notes }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
