@extends('layouts.employee')

@section('title', 'KPI Saya')
@section('page-title', 'Detail KPI Saya')

@section('content')

@php
    $kpiColor = \App\Services\KpiService::color($summary['percent']);
    $kpiText  = ['green'=>'text-green-600 dark:text-green-400','amber'=>'text-amber-500 dark:text-amber-400','red'=>'text-red-500 dark:text-red-400','gray'=>'text-gray-400'][$kpiColor];
    $delta    = $summary['trend']['delta'] ?? null;
@endphp

{{-- Ringkasan + breakdown --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Skor utama --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">KPI {{ $summary['period_label'] }}</p>
        <div class="flex items-end gap-2 mt-2">
            <p class="text-4xl font-bold {{ $kpiText }}">{{ $summary['percent'] === null ? '—' : $summary['percent'].'%' }}</p>
            @if($delta !== null && $delta !== 0)
                <span class="mb-1.5 text-xs font-medium {{ $delta > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">{{ $delta > 0 ? '▲' : '▼' }}{{ abs($delta) }}%</span>
            @endif
        </div>
        <div class="flex items-center justify-between mt-2">
            <span class="text-sm font-medium {{ $kpiText }}">{{ $summary['band_label'] }}</span>
            <span class="text-xs text-gray-400 dark:text-gray-500">Target {{ $summary['target'] }}%</span>
        </div>
        {{-- progress bar vs target --}}
        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden mt-3 relative">
            <div class="h-full rounded-full {{ ['green'=>'bg-green-500','amber'=>'bg-amber-500','red'=>'bg-red-500','gray'=>'bg-gray-400'][$kpiColor] }}"
                 style="width: {{ $summary['percent'] ?? 0 }}%"></div>
            <div class="absolute top-0 bottom-0 w-0.5 bg-gray-800 dark:bg-gray-200" style="left: {{ $summary['target'] }}%" title="Target"></div>
        </div>
    </div>

    {{-- Breakdown 3 dimensi --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Rincian Dimensi</h2>
        @if($summary['percent'] === null)
            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada tugas jatuh tempo bulan ini.</p>
        @else
        <div class="space-y-3">
            @foreach([
                ['Quality (mutu, 50%)', $summary['breakdown']['quality'], 'bg-blue-500'],
                ['On-Time (ketepatan, 30%)', $summary['breakdown']['ontime'], 'bg-green-500'],
                ['Completion (penyelesaian, 20%)', $summary['breakdown']['completion'], 'bg-amber-500'],
            ] as [$label,$val,$bar])
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600 dark:text-gray-300">{{ $label }}</span>
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $val !== null ? rtrim(rtrim(number_format($val,1),'0'),'.').'%' : '—' }}</span>
                </div>
                <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full rounded-full {{ $bar }}" style="width: {{ $val !== null ? min(100,$val) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">
            {{ $summary['approved'] }}/{{ $summary['due_in_period'] }} tugas disetujui ·
            {{ $summary['on_time'] }} tepat waktu · {{ $summary['total_revisions'] }} revisi ·
            rumus: 50%·Quality + 30%·OnTime + 20%·Completion
        </p>
        @endif
    </div>
</div>

{{-- Tren 6 bulan --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-4 transition-colors duration-200">
    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Tren KPI 6 Bulan</h2>
    <div class="relative h-56">
        <canvas id="kpiTrendChart"></canvas>
    </div>
</div>

{{-- Daftar tugas + skor + alasan --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Tugas Bulan Ini & Skornya</h2>
    @if($tasks->isEmpty())
        <x-empty-state title="Belum ada tugas disetujui bulan ini" />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 dark:border-gray-700">
                <tr>
                    <th class="text-left text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5">Tugas</th>
                    <th class="text-left text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5 hidden sm:table-cell">Prioritas</th>
                    <th class="text-center text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5">Mutu</th>
                    <th class="text-center text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5 hidden sm:table-cell">Tepat waktu</th>
                    <th class="text-left text-xs font-medium text-gray-400 dark:text-gray-500 pb-2.5">Alasan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($tasks as $a)
                <tr>
                    <td class="py-2.5 pr-3">
                        <a href="{{ route('employee.tasks.show', $a->id) }}" class="font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">{{ $a->task?->title }}</a>
                    </td>
                    <td class="py-2.5 pr-3 hidden sm:table-cell"><x-priority-badge :priority="$a->task?->priority ?? 'medium'" /></td>
                    <td class="py-2.5 text-center">
                        <span class="font-semibold {{ $a->kpi_quality >= 8 ? 'text-green-600 dark:text-green-400' : ($a->kpi_quality >= 5 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $a->kpi_quality }}/10</span>
                    </td>
                    <td class="py-2.5 text-center hidden sm:table-cell">
                        @if($a->kpi_ontime)
                            <span class="text-green-500">✓</span>
                        @else
                            <span class="text-red-500">✗</span>
                        @endif
                    </td>
                    <td class="py-2.5 text-xs text-gray-500 dark:text-gray-400">{{ $a->kpi_reason }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3">
        Mutu = max(0, 10 − 2 × revisi). Telat & revisi menurunkan skor. "Tepat waktu" dihitung dari jam submit vs tenggat.
    </p>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('kpiTrendChart');
    if (ctx) {
        const trend = @json($trend);
        const isDark = document.documentElement.classList.contains('dark');
        const grid = isDark ? 'rgba(255,255,255,0.08)' : '#f1f5f9';
        const tick = isDark ? '#94a3b8' : '#64748b';
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trend.map(d => d.label),
                datasets: [{
                    label: 'KPI %',
                    data: trend.map(d => d.percent),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.12)',
                    fill: true,
                    tension: 0.35,
                    spanGaps: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: grid }, ticks: { color: tick, font: { size: 11 } } },
                    y: { beginAtZero: true, max: 100, grid: { color: grid }, ticks: { color: tick, stepSize: 20, font: { size: 11 } } },
                }
            }
        });
    }
</script>
@endpush
