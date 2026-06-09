@extends('layouts.employee')

@section('title', 'Aktivitas Saya')
@section('page-title', 'Aktivitas Saya')

@section('content')

{{-- Filter --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-5 transition-colors duration-200">
    <form method="GET" action="{{ route('employee.activity.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-3">
        <select name="type"
                class="px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-48">
            <option value="">Semua Jenis</option>
            @foreach(['created'=>'Tugas dibuat','started'=>'Mulai dikerjakan','submitted'=>'Laporan dikirim','revised'=>'Diminta revisi','approved'=>'Disetujui'] as $v=>$l)
            <option value="{{ $v }}" {{ request('type')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}"
               class="px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" name="to" value="{{ request('to') }}"
               class="px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white rounded-xl" style="background:linear-gradient(135deg,#1d4ed8,#2563eb)">Filter</button>
        @if(request()->hasAny(['type','from','to']))
        <a href="{{ route('employee.activity.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-center">Reset</a>
        @endif
    </form>
</div>

{{-- Timeline lintas tugas --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200">
    @if($logs->isEmpty())
        <x-empty-state title="Belum ada aktivitas" description="Aktivitas Anda lintas tugas akan tampil di sini." />
    @else
        <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-2 space-y-5">
            @foreach($logs as $log)
            @php
                $p = $log->presentation();
                $dot = ['gray'=>'bg-gray-300 dark:bg-gray-600','blue'=>'bg-blue-500','indigo'=>'bg-indigo-500','amber'=>'bg-amber-400','green'=>'bg-green-500'][$p['color']] ?? 'bg-gray-300';
            @endphp
            <li class="ml-4">
                <span class="absolute -left-[7px] mt-1 w-3 h-3 rounded-full ring-4 ring-white dark:ring-gray-800 {{ $dot }}"></span>
                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $p['label'] }}</p>
                    <span class="text-gray-400 dark:text-gray-500">·</span>
                    <a href="{{ route('employee.tasks.show', $log->assignment_id) }}"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ $log->assignment?->task?->title ?? 'Tugas' }}</a>
                    @if($log->type === 'approved' && isset($log->meta['kpi']))
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 font-semibold">KPI {{ $log->meta['kpi'] }}</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</p>
                @if($log->notes)
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-2.5 py-1.5">{{ $log->notes }}</p>
                @endif
            </li>
            @endforeach
        </ol>

        <div class="mt-5">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
