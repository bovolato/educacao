@props(['color' => 'gray', 'dot' => false])

@php
$colors = [
    'green'  => 'bg-green-100 text-green-800',
    'red'    => 'bg-red-100 text-red-800',
    'yellow' => 'bg-yellow-100 text-yellow-800',
    'blue'   => 'bg-blue-100 text-blue-800',
    'indigo' => 'bg-indigo-100 text-indigo-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'gray'   => 'bg-gray-100 text-gray-700',
    'orange' => 'bg-orange-100 text-orange-800',
];
$dotColors = [
    'green'  => 'bg-green-500',
    'red'    => 'bg-red-500',
    'yellow' => 'bg-yellow-500',
    'blue'   => 'bg-blue-500',
    'indigo' => 'bg-indigo-500',
    'purple' => 'bg-purple-500',
    'gray'   => 'bg-gray-400',
    'orange' => 'bg-orange-500',
];
$cls = $colors[$color] ?? $colors['gray'];
$dotCls = $dotColors[$color] ?? $dotColors['gray'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cls }}">
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotCls }}"></span>
    @endif
    {{ $slot }}
</span>
