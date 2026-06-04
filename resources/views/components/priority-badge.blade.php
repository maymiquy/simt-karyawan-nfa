@props(['priority'])

@php
$map = [
    'high'   => ['label' => 'Tinggi', 'dot' => 'bg-red-500',   'class' => 'bg-red-50 text-red-700 border-red-100'],
    'medium' => ['label' => 'Sedang', 'dot' => 'bg-amber-400', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
    'low'    => ['label' => 'Rendah', 'dot' => 'bg-green-400', 'class' => 'bg-green-50 text-green-700 border-green-100'],
];
$badge = $map[$priority] ?? ['label' => $priority, 'dot' => 'bg-gray-400', 'class' => 'bg-gray-100 text-gray-600 border-gray-200'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium border ' . $badge['class']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
    {{ $badge['label'] }}
</span>
