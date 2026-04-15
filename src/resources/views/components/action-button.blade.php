@props(['href' => null, 'type' => 'button', 'variant' => 'primary', 'size' => 'md', 'icon' => null])

@php
$variants = [
    'primary'   => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm',
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm',
    'danger'    => 'bg-red-600 hover:bg-red-700 text-white shadow-sm',
    'success'   => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm',
    'ghost'     => 'text-gray-600 hover:bg-gray-100',
];
$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-sm',
];
$cls = ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
$cls .= ' inline-flex items-center gap-2 font-medium rounded-xl transition-colors';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class([$cls]) }}>
        @if($icon)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$cls]) }}>
        @if($icon)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
