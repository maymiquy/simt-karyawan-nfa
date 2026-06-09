@extends('layouts.employee')

@section('title', 'Kalender Tenggat')
@section('page-title', 'Kalender Tenggat')

@section('content')

@php
    use Illuminate\Support\Carbon;
    $first      = $month->copy()->startOfMonth();
    $daysInMonth= $month->daysInMonth;
    $leadBlanks = $first->dayOfWeek; // 0=Min ... 6=Sab
    $prevMonth  = $month->copy()->subMonthNoOverflow()->format('Y-m');
    $nextMonth  = $month->copy()->addMonthNoOverflow()->format('Y-m');
    $today      = Carbon::today();

    $priorityDot = ['high'=>'bg-red-500','medium'=>'bg-amber-400','low'=>'bg-green-400'];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-5 transition-colors duration-200">

    {{-- Header bulan + navigasi --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('employee.calendar.index', ['month' => $prevMonth]) }}"
           class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="text-center">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $month->translatedFormat('F Y') }}</h2>
            <a href="{{ route('employee.calendar.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Hari ini</a>
        </div>
        <a href="{{ route('employee.calendar.index', ['month' => $nextMonth]) }}"
           class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Nama hari --}}
    <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-1">
        @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
        <div class="text-center text-[11px] font-medium text-gray-400 dark:text-gray-500 py-1">{{ $d }}</div>
        @endforeach
    </div>

    {{-- Grid tanggal --}}
    <div class="grid grid-cols-7 gap-1 sm:gap-2">
        {{-- sel kosong sebelum tanggal 1 --}}
        @for($i = 0; $i < $leadBlanks; $i++)
            <div class="aspect-square sm:aspect-[4/3]"></div>
        @endfor

        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $date    = $month->copy()->day($day);
                $isToday = $date->isSameDay($today);
                $items   = $byDay[$day] ?? collect();
            @endphp
            <div class="aspect-square sm:aspect-[4/3] rounded-lg border p-1 sm:p-1.5 overflow-hidden flex flex-col
                        {{ $isToday ? 'border-blue-400 dark:border-blue-500 bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-700' }}">
                <span class="text-[11px] font-medium {{ $isToday ? 'text-blue-600 dark:text-blue-300' : 'text-gray-500 dark:text-gray-400' }}">{{ $day }}</span>
                <div class="flex-1 mt-0.5 space-y-0.5 overflow-hidden">
                    @foreach($items->take(3) as $a)
                    @php $isOverdue = $a->task->due_date->isPast() && $a->progress !== 'done'; @endphp
                    <a href="{{ route('employee.tasks.show', $a->id) }}"
                       class="flex items-center gap-1 px-1 py-0.5 rounded text-[10px] leading-tight truncate
                              {{ $a->progress === 'done' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : ($isOverdue ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300') }}"
                       title="{{ $a->task->title }} — {{ $a->task->due_date->format('H:i') }}">
                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $priorityDot[$a->task->priority] ?? 'bg-gray-400' }}"></span>
                        <span class="truncate">{{ $a->task->title }}</span>
                    </a>
                    @endforeach
                    @if($items->count() > 3)
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 px-1">+{{ $items->count() - 3 }} lagi</p>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    {{-- Legenda --}}
    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-[11px] text-gray-500 dark:text-gray-400">
        <span class="font-medium">Prioritas:</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Tinggi</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Sedang</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-400"></span> Rendah</span>
        <span class="ml-2 font-medium">Status:</span>
        <span class="flex items-center gap-1"><span class="w-3 h-2 rounded bg-green-100 dark:bg-green-900/40"></span> Selesai</span>
        <span class="flex items-center gap-1"><span class="w-3 h-2 rounded bg-red-100 dark:bg-red-900/40"></span> Terlambat</span>
    </div>
</div>

@endsection
