@props(['status'])

@php
$map = [
    'pending'     => ['label' => 'Pending',      'class' => 'bg-gray-100 text-gray-600 border-gray-200'],
    'in_progress' => ['label' => 'Proses',        'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
    'completed'   => ['label' => 'Selesai',       'class' => 'bg-green-50 text-green-700 border-green-100'],
    'overdue'     => ['label' => 'Terlambat',     'class' => 'bg-red-50 text-red-700 border-red-100'],
    'cancelled'   => ['label' => 'Dibatalkan',    'class' => 'bg-gray-100 text-gray-500 border-gray-200'],
    'not_started' => ['label' => 'Belum Mulai',   'class' => 'bg-gray-100 text-gray-600 border-gray-200'],
    'on_progress' => ['label' => 'Sedang Proses', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
    'done'        => ['label' => 'Selesai',       'class' => 'bg-green-50 text-green-700 border-green-100'],
    'revision'    => ['label' => 'Perlu Revisi',  'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
];
$badge = $map[$status] ?? ['label' => $status, 'class' => 'bg-gray-100 text-gray-600 border-gray-200'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border ' . $badge['class']]) }}>
    {{ $badge['label'] }}
</span>
