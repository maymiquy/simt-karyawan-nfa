@props([
    'percent' => null,   // int|null
    'size'    => 'sm',    // sm | lg
])

@php
$color = \App\Services\KpiService::color($percent);
$styles = [
    'green' => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-100 dark:border-green-800',
    'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border-amber-100 dark:border-amber-800',
    'red'   => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-100 dark:border-red-800',
    'gray'  => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-600',
];
$cls  = $styles[$color] ?? $styles['gray'];
$pad  = $size === 'lg' ? 'px-3 py-1 text-sm' : 'px-2 py-0.5 text-xs';
$dot  = ['green' => 'bg-green-500', 'amber' => 'bg-amber-400', 'red' => 'bg-red-500', 'gray' => 'bg-gray-400'][$color];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-md font-semibold border $cls $pad"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
    @if($percent === null)
        KPI: —
    @else
        KPI: {{ $percent }}%
    @endif
</span>
