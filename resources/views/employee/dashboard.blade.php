@extends('layouts.employee')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Tugas Aktif --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Tugas Aktif</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-blue-50">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $activeCount }}</p>
        <p class="text-xs text-gray-400 mt-1">Sedang dikerjakan</p>
    </div>

    {{-- Tugas Selesai --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Tugas Selesai</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-green-50">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $doneCount }}</p>
        <p class="text-xs text-gray-400 mt-1">Laporan dikirim</p>
    </div>

    {{-- Tenggat Dekat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-500">Tenggat Dekat</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-amber-50">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $nearDueCount }}</p>
        <p class="text-xs text-gray-400 mt-1">Dalam 3 hari ke depan</p>
    </div>
</div>

{{-- Progress & Recent --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Progress Keseluruhan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Progres Keseluruhan</h2>

        <div class="flex items-center justify-center mb-4">
            <div class="relative w-28 h-28">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#f1f5f9" stroke-width="10"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#3b82f6" stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ round($completionRate * 2.39) }} 239"
                            style="transition: stroke-dasharray 0.6s ease"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</span>
                </div>
            </div>
        </div>

        <div class="space-y-2.5 text-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                    <span class="text-gray-600">Selesai</span>
                </div>
                <span class="font-semibold text-gray-800">{{ $doneCount }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-gray-600">Aktif</span>
                </div>
                <span class="font-semibold text-gray-800">{{ $activeCount }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                    <span class="text-gray-600">Total</span>
                </div>
                <span class="font-semibold text-gray-800">{{ $totalCount }}</span>
            </div>
        </div>
    </div>

    {{-- Tugas Terbaru --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700">Tugas Terbaru</h2>
            <a href="{{ route('employee.tasks.index') }}"
               class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                Lihat semua →
            </a>
        </div>

        @if ($recentAssignments->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">Belum ada tugas yang diberikan</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($recentAssignments as $assignment)
                <a href="{{ route('employee.tasks.show', $assignment->id) }}"
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">

                    {{-- Priority dot --}}
                    <div class="flex-shrink-0 w-2 h-2 rounded-full mt-0.5
                        @if($assignment->task?->priority === 'high') bg-red-500
                        @elseif($assignment->task?->priority === 'medium') bg-amber-400
                        @else bg-green-400 @endif">
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate group-hover:text-blue-600 transition-colors">
                            {{ $assignment->task?->title ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if ($assignment->task?->due_date)
                                Tenggat: {{ $assignment->task->due_date->translatedFormat('d M Y') }}
                            @else
                                Tanpa tenggat
                            @endif
                        </p>
                    </div>

                    @php
                        $progressLabels = [
                            'not_started' => ['label' => 'Belum Mulai', 'class' => 'bg-gray-100 text-gray-600'],
                            'on_progress' => ['label' => 'Proses', 'class' => 'bg-blue-50 text-blue-700'],
                            'done'        => ['label' => 'Selesai', 'class' => 'bg-green-50 text-green-700'],
                            'revision'    => ['label' => 'Revisi', 'class' => 'bg-amber-50 text-amber-700'],
                        ];
                        $badge = $progressLabels[$assignment->progress] ?? ['label' => $assignment->progress, 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium {{ $badge['class'] }}">
                        {{ $badge['label'] }}
                    </span>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
