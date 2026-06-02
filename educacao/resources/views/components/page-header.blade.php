@props([
    'title',
    'subtitle' => null,
    'backRoute' => null,
    'backLabel' => 'Voltar',
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        @if($backRoute)
            <a href="{{ $backRoute }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600 mb-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ $backLabel }}
            </a>
        @endif
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-3 shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
