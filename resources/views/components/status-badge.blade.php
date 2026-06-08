@props(['status'])

@php
$map = [
    'pending'     => ['label' => 'Pending',        'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600'],
    'in_progress' => ['label' => 'Proses',         'class' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-100 dark:border-blue-800'],
    'completed'   => ['label' => 'Selesai',        'class' => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-100 dark:border-green-800'],
    'overdue'     => ['label' => 'Terlambat',      'class' => 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-100 dark:border-red-800'],
    'cancelled'   => ['label' => 'Dibatalkan',     'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-600'],
    'not_started' => ['label' => 'Belum Mulai',    'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600'],
    'on_progress' => ['label' => 'Sedang Proses',  'class' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-100 dark:border-blue-800'],
    'submitted'   => ['label' => 'Menunggu Review','class' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border-indigo-100 dark:border-indigo-800'],
    'done'        => ['label' => 'Disetujui',      'class' => 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-100 dark:border-green-800'],
    'revision'    => ['label' => 'Perlu Revisi',   'class' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border-amber-100 dark:border-amber-800'],
];
$badge = $map[$status] ?? ['label' => $status, 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border ' . $badge['class']]) }}>
    {{ $badge['label'] }}
</span>
