@props(['title' => null, 'subtitle' => null])

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
    @if($title)
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
