@props([
    'logs',            // Collection<AssignmentLog>
    'showActor' => true,
])

@php
$dotColors = [
    'gray'   => 'bg-gray-300 dark:bg-gray-600',
    'blue'   => 'bg-blue-500',
    'indigo' => 'bg-indigo-500',
    'amber'  => 'bg-amber-400',
    'green'  => 'bg-green-500',
];
@endphp

@if($logs->isEmpty())
    <p class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada aktivitas.</p>
@else
<ol class="relative border-l border-gray-200 dark:border-gray-700 ml-2 space-y-4">
    @foreach($logs as $log)
    @php $p = $log->presentation(); $dot = $dotColors[$p['color']] ?? $dotColors['gray']; @endphp
    <li class="ml-4">
        <span class="absolute -left-[7px] mt-1 w-3 h-3 rounded-full ring-4 ring-white dark:ring-gray-800 {{ $dot }}"></span>
        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $p['label'] }}</p>
            @if($log->type === 'revised')
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 font-semibold">REVISI</span>
            @elseif($log->type === 'approved' && isset($log->meta['kpi']))
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 font-semibold">KPI {{ $log->meta['kpi'] }}</span>
            @endif
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500">
            @if($showActor && $log->user){{ $log->user->name }} · @endif
            {{ $log->created_at->translatedFormat('d M Y, H:i') }}
        </p>
        @if($log->notes)
            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-2.5 py-1.5">
                {{ $log->notes }}
            </p>
        @endif
        @if($log->type === 'submitted' && isset($log->meta['activity_count']))
            <p class="mt-1 text-[11px] text-indigo-600 dark:text-indigo-400">
                {{ $log->meta['activity_count'] }} aktivitas dilaporkan
                @isset($log->meta['blocked_count'])
                    @if($log->meta['blocked_count'] > 0)· {{ $log->meta['blocked_count'] }} terkendala @endif
                @endisset
            </p>
        @endif
    </li>
    @endforeach
</ol>
@endif
