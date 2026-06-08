@props([
    'label',
    'value',
    'description' => null,
    'color'       => 'blue',
    'icon'        => null,
    'href'        => null,
])

@php
$colors = [
    'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',   'icon' => 'text-blue-600 dark:text-blue-400'],
    'green'  => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'icon' => 'text-green-600 dark:text-green-400'],
    'amber'  => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'icon' => 'text-amber-500 dark:text-amber-400'],
    'red'    => ['bg' => 'bg-red-50 dark:bg-red-900/20',     'icon' => 'text-red-500 dark:text-red-400'],
    'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20','icon'=> 'text-purple-600 dark:text-purple-400'],
    'gray'   => ['bg' => 'bg-gray-100 dark:bg-gray-700',     'icon' => 'text-gray-500 dark:text-gray-400'],
];
$valColors = [
    'red' => 'text-red-600 dark:text-red-400',
];
$c   = $colors[$color] ?? $colors['blue'];
$val = $valColors[$color] ?? 'text-gray-900 dark:text-white';
$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' =>
        'bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-colors duration-200'
        . ($href ? ' hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600' : '')
    ]) }}>

    <div class="flex items-center justify-between mb-3">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
        @if($icon)
        <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $c['bg'] }}">
            <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        </div>
        @endif
    </div>

    <p class="text-3xl font-bold {{ $val }}">{{ $value }}</p>

    @if($description)
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $description }}</p>
    @endif
</{{ $tag }}>
