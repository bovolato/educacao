@props([
    'title' => 'Nada por aqui',
    'subtitle' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
    <p class="text-gray-800 font-semibold mb-1">{{ $title }}</p>
    @if($subtitle)
        <p class="text-sm text-gray-500 mb-5">{{ $subtitle }}</p>
    @endif
    @if($actionHref && $actionLabel)
        <a href="{{ $actionHref }}"
            class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
            {{ $actionLabel }}
        </a>
    @endif
</div>

