{{--
    Toast notification — letakkan sekali di layout, otomatis tampil dari session flash.
    Usage: <x-toast />
--}}

@if(session('success') || session('error') || session('warning') || session('info'))
@php
    $type = session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info'));
    $message = session($type);
    $styles = [
        'success' => ['border' => 'border-green-200',  'bg' => 'bg-green-50',  'text' => 'text-green-700',  'icon_color' => 'text-green-500'],
        'error'   => ['border' => 'border-red-200',    'bg' => 'bg-red-50',    'text' => 'text-red-700',    'icon_color' => 'text-red-500'],
        'warning' => ['border' => 'border-amber-200',  'bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'icon_color' => 'text-amber-500'],
        'info'    => ['border' => 'border-blue-200',   'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'icon_color' => 'text-blue-500'],
    ];
    $s = $styles[$type];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 4500)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-5 right-5 z-50 max-w-sm w-full"
    style="display:none">

    <div class="flex items-start gap-3 rounded-xl border {{ $s['border'] }} {{ $s['bg'] }} px-4 py-3 shadow-lg">

        {{-- Icon --}}
        <div class="flex-shrink-0 mt-0.5">
            @if($type === 'success')
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            @elseif($type === 'error')
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            @elseif($type === 'warning')
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            @else
            <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            @endif
        </div>

        <p class="flex-1 text-sm font-medium {{ $s['text'] }}">{{ $message }}</p>

        <button @click="show = false"
                class="flex-shrink-0 {{ $s['icon_color'] }} hover:opacity-70 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif
