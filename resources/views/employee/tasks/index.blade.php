@extends('layouts.employee')

@section('title', 'Tugas Saya')
@section('page-title', 'Tugas Saya')

@section('content')

{{-- Filter bar --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <form method="GET" action="{{ route('employee.tasks.index') }}" class="flex flex-col sm:flex-row gap-3">

        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul tugas..."
                   class="block w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                          hover:border-gray-300 transition-colors">
        </div>

        {{-- Status filter --}}
        <select name="status"
                class="px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                       hover:border-gray-300 transition-colors text-gray-700 sm:w-44">
            <option value="">Semua Status</option>
            <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Belum Mulai</option>
            <option value="on_progress" {{ request('status') === 'on_progress' ? 'selected' : '' }}>Sedang Proses</option>
            <option value="done"        {{ request('status') === 'done'        ? 'selected' : '' }}>Selesai</option>
            <option value="revision"    {{ request('status') === 'revision'    ? 'selected' : '' }}>Perlu Revisi</option>
        </select>

        <button type="submit"
                class="px-4 py-2.5 text-sm font-medium text-white rounded-xl transition-all
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                style="background: linear-gradient(135deg, #1d4ed8, #2563eb)">
            Filter
        </button>

        @if (request()->hasAny(['search', 'status']))
        <a href="{{ route('employee.tasks.index') }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-center">
            Reset
        </a>
        @endif
    </form>
</div>

{{-- Task list --}}
@if ($assignments->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 flex flex-col items-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-gray-500 font-medium">Tidak ada tugas ditemukan</p>
        <p class="text-gray-400 text-sm mt-1">
            @if (request()->hasAny(['search', 'status']))
                Coba ubah filter pencarian Anda.
            @else
                Belum ada tugas yang diberikan kepada Anda.
            @endif
        </p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($assignments as $assignment)
        @php
            $task = $assignment->task;
            $isOverdue = $task?->due_date && $task->due_date->isPast() && ! in_array($assignment->progress, ['done']);
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border transition-all hover:shadow-md
                    {{ $isOverdue ? 'border-red-200' : 'border-gray-100' }}">
            <div class="p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-start gap-3">

                    {{-- Main info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            {{-- Priority badge --}}
                            @if ($task?->priority === 'high')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Tinggi
                                </span>
                            @elseif ($task?->priority === 'medium')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Sedang
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Rendah
                                </span>
                            @endif

                            {{-- Progress badge --}}
                            @php
                                $progressMap = [
                                    'not_started' => ['label' => 'Belum Mulai', 'class' => 'bg-gray-100 text-gray-600 border-gray-200'],
                                    'on_progress' => ['label' => 'Sedang Proses', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
                                    'done'        => ['label' => 'Selesai', 'class' => 'bg-green-50 text-green-700 border-green-100'],
                                    'revision'    => ['label' => 'Perlu Revisi', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
                                ];
                                $pb = $progressMap[$assignment->progress] ?? ['label' => $assignment->progress, 'class' => 'bg-gray-100 text-gray-600 border-gray-200'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border {{ $pb['class'] }}">
                                {{ $pb['label'] }}
                            </span>

                            @if ($isOverdue)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                    Terlambat
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm font-semibold text-gray-900 mb-1">
                            {{ $task?->title ?? '—' }}
                        </h3>

                        @if ($task?->description)
                            <p class="text-xs text-gray-500 line-clamp-2">{{ $task->description }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-3 mt-2.5">
                            @if ($task?->due_date)
                                <span class="flex items-center gap-1.5 text-xs {{ $isOverdue ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $task->due_date->translatedFormat('d M Y') }}
                                </span>
                            @endif

                            @if ($task?->creator)
                                <span class="flex items-center gap-1.5 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $task->creator->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="sm:flex-shrink-0">
                        <a href="{{ route('employee.tasks.show', $assignment->id) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-blue-700
                                  bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors border border-blue-100">
                            Lihat Detail
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($assignments->hasPages())
    <div class="mt-5">
        {{ $assignments->links() }}
    </div>
    @endif
@endif

@endsection
