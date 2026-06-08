@extends('layouts.employee')

@section('title', 'Tugas Saya')
@section('page-title', 'Tugas Saya')

@section('content')

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-5 transition-colors duration-200"
     x-data="{
         search: '{{ request('search') }}',
         status: '{{ request('status') }}',
         submit() { this.$refs.form.submit(); }
     }">

    <form method="GET" action="{{ route('employee.tasks.index') }}" x-ref="form" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" x-model="search" @input.debounce.400ms="submit()"
                   placeholder="Cari judul tugas..."
                   class="block w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl
                          placeholder-gray-400 dark:placeholder-gray-500
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors">
        </div>

        <select name="status" x-model="status" @change="submit()"
                class="px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-xl
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300 dark:hover:border-gray-500 transition-colors sm:w-44">
            <option value="">Semua Status</option>
            <option value="not_started">Belum Mulai</option>
            <option value="on_progress">Sedang Proses</option>
            <option value="submitted">Menunggu Review</option>
            <option value="done">Disetujui</option>
            <option value="revision">Perlu Revisi</option>
        </select>

        @if (request()->hasAny(['search', 'status']))
        <a href="{{ route('employee.tasks.index') }}"
           class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-center shrink-0">
            Reset
        </a>
        @endif
    </form>

    @if (request()->hasAny(['search', 'status']))
    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
        <span class="text-xs text-gray-400 dark:text-gray-500">Filter aktif:</span>
        @if (request('search'))
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium border border-blue-100 dark:border-blue-800">
                "{{ request('search') }}"
                <a href="{{ route('employee.tasks.index', ['status' => request('status')]) }}" class="hover:opacity-70">×</a>
            </span>
        @endif
        @if (request('status'))
            @php $sl = ['not_started'=>'Belum Mulai','on_progress'=>'Proses','submitted'=>'Menunggu Review','done'=>'Disetujui','revision'=>'Revisi']; @endphp
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium border border-blue-100 dark:border-blue-800">
                {{ $sl[request('status')] ?? request('status') }}
                <a href="{{ route('employee.tasks.index', ['search' => request('search')]) }}" class="hover:opacity-70">×</a>
            </span>
        @endif
    </div>
    @endif
</div>

@if ($assignments->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
        <x-empty-state
            title="Tidak ada tugas ditemukan"
            :description="request()->hasAny(['search','status']) ? 'Coba ubah filter pencarian Anda.' : 'Belum ada tugas yang diberikan kepada Anda.'"
        />
    </div>
@else
    <div class="space-y-3">
        @foreach ($assignments as $assignment)
        @php $task=$assignment->task; $isOverdue=$task?->due_date&&$task->due_date->isPast()&&!in_array($assignment->progress,['done']); @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border transition-all hover:shadow-md
                    {{ $isOverdue ? 'border-red-200 dark:border-red-800' : 'border-gray-100 dark:border-gray-700' }} transition-colors duration-200">
            <div class="p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <x-priority-badge :priority="$task?->priority ?? 'medium'"/>
                            <x-status-badge :status="$assignment->progress"/>
                            @if ($isOverdue)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-100 dark:border-red-800">Terlambat</span>
                            @endif
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $task?->title ?? '—' }}</h3>
                        @if ($task?->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $task->description }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 mt-2.5">
                            @if ($task?->due_date)
                            <span class="flex items-center gap-1.5 text-xs {{ $isOverdue ? 'text-red-500 dark:text-red-400 font-medium' : 'text-gray-400 dark:text-gray-500' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $task->due_date->translatedFormat('d M Y') }}
                                @if($task->due_date->isToday()) <span class="text-amber-500">(hari ini)</span>
                                @elseif($task->due_date->isTomorrow()) <span class="text-amber-500">(besok)</span>
                                @endif
                            </span>
                            @endif
                            @if ($task?->creator)
                            <span class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $task->creator->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('employee.tasks.show', $assignment->id) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-blue-700 dark:text-blue-300
                              bg-blue-50 dark:bg-blue-900/30 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-800/40 transition-colors border border-blue-100 dark:border-blue-800 shrink-0">
                        Lihat Detail
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @if ($assignments->hasPages())
    <div class="mt-5">{{ $assignments->links() }}</div>
    @endif
@endif

@endsection
