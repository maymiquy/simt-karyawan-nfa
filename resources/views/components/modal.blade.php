@props([
    'id',
    'title'    => '',
    'maxWidth' => 'md',   // sm | md | lg | xl
])

@php
$widths = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-xl'];
$w = $widths[$maxWidth] ?? 'max-w-md';
@endphp

<div id="{{ $id }}"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     x-data
     @keydown.escape.window="document.getElementById('{{ $id }}').classList.add('hidden')">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>

    {{-- Box --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full {{ $w }} z-10
                animate-[fadeIn_0.15s_ease-out]">

        {{-- Header --}}
        @if($title)
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
            <button onclick="document.getElementById('{{ $id }}').classList.add('hidden')"
                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
