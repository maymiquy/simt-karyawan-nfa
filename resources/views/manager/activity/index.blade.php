@extends('layouts.manager')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Riwayat tugas & revisi per karyawan')

@section('content')

@if ($employees->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
        <x-empty-state title="Belum ada aktivitas" description="Belum ada karyawan dengan tugas yang tercatat." />
    </div>
@else
<div class="space-y-3">
    @foreach ($employees as $emp)
    @php $empAssignments = $assignments[$emp->id] ?? collect(); @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-200"
         x-data="{ open: false }">

        {{-- Header karyawan (klik untuk expand) --}}
        <button @click="open = !open" class="w-full flex items-center gap-3 p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                 style="background:linear-gradient(135deg,#3b82f6,#1d4ed8)">
                {{ strtoupper(substr($emp->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $emp->name }}</p>
                    <x-kpi-badge :percent="$emp->kpi['percent']" />
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    {{ $empAssignments->count() }} tugas ·
                    {{ $emp->kpi['approved'] }} disetujui ·
                    {{ $emp->kpi['total_revisions'] }} total revisi
                </p>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Body: daftar tugas + timeline --}}
        <div x-show="open" x-transition style="display:none" class="border-t border-gray-100 dark:border-gray-700">
            <div class="p-4 space-y-3">
                @forelse ($empAssignments as $a)
                <div class="rounded-xl border border-gray-100 dark:border-gray-700 p-4">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="min-w-0">
                            <a href="{{ route('manager.tasks.show', $a->task_id) }}"
                               class="text-sm font-semibold text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $a->task?->title ?? '—' }}
                            </a>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                Tenggat: {{ $a->task?->due_date?->translatedFormat('d M Y, H:i') ?? '—' }}
                                @if($a->workDurationHuman()) · Durasi: {{ $a->workDurationHuman() }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($a->kpi_score !== null)
                                <span class="text-xs font-bold px-2 py-0.5 rounded
                                    {{ $a->kpi_score >= 8 ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : ($a->kpi_score >= 5 ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300') }}">
                                    {{ rtrim(rtrim(number_format($a->kpi_score,1),'0'),'.') }}/10
                                </span>
                            @endif
                            <x-status-badge :status="$a->progress" />
                        </div>
                    </div>

                    {{-- Aktivitas dilaporkan (ringkas) --}}
                    @if($a->activities->isNotEmpty())
                    <ul class="mb-3 space-y-1">
                        @foreach($a->activities as $act)
                        <li class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                            <span class="w-1.5 h-1.5 rounded-full {{ $act->status === 'done' ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                            {{ $act->description }}
                            @if($act->status === 'blocked')<span class="text-amber-600 dark:text-amber-400">(terkendala)</span>@endif
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    {{-- Timeline lifecycle --}}
                    <x-assignment-timeline :logs="$a->logs" :show-actor="true" />
                </div>
                @empty
                <p class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada tugas.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
