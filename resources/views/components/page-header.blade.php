@props([
    'title',
    'breadcrumbs' => [],  // [['label' => '...', 'url' => '...'], ...]
])

<div {{ $attributes->merge(['class' => 'mb-5']) }}>
    {{-- Breadcrumb --}}
    @if(count($breadcrumbs))
    <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-2">
        @foreach($breadcrumbs as $i => $crumb)
            @if($i > 0)<span>/</span>@endif
            @if(isset($crumb['url']))
                <a href="{{ $crumb['url'] }}" class="hover:text-gray-600 transition-colors">{{ $crumb['label'] }}</a>
            @else
                <span class="text-gray-600 font-medium">{{ $crumb['label'] }}</span>
            @endif
        @endforeach
    </nav>
    @endif

    <div class="flex items-center justify-between gap-4">
        <h1 class="text-lg font-bold text-gray-900">{{ $title }}</h1>
        @if($slot->isNotEmpty())
        <div class="flex items-center gap-2 flex-shrink-0">
            {{ $slot }}
        </div>
        @endif
    </div>
</div>
