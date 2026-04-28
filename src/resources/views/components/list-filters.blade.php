@props([
    /** string */
    'action' => null,
    /** array<array{type:string,name:string,label:string,options?:array,placeholder?:string}> */
    'fields' => [],
])

@php
    $actionUrl = $action ? url($action) : url()->current();
@endphp

<form method="GET" action="{{ $actionUrl }}" class="mb-5">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach($fields as $f)
                @php
                    $type = $f['type'] ?? 'text';
                    $name = $f['name'];
                    $label = $f['label'] ?? $name;
                    $placeholder = $f['placeholder'] ?? null;
                    $val = request($name);
                @endphp
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
                    @if($type === 'select')
                        <select name="{{ $name }}" class="w-full rounded-xl border-gray-300 text-sm">
                            <option value="">{{ $placeholder ?? 'Todos' }}</option>
                            @foreach(($f['options'] ?? []) as $optVal => $optLabel)
                                <option value="{{ $optVal }}" @selected((string) $val === (string) $optVal)>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                    @elseif($type === 'date')
                        <input type="date" name="{{ $name }}" value="{{ $val }}" class="w-full rounded-xl border-gray-300 text-sm">
                    @else
                        <input type="text" name="{{ $name }}" value="{{ $val }}" placeholder="{{ $placeholder ?? '' }}"
                            class="w-full rounded-xl border-gray-300 text-sm" />
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-gray-500">Dica: use os filtros para achar rapidamente.</div>
            <div class="flex items-center gap-2">
                <a href="{{ url()->current() }}" class="text-sm text-gray-600 hover:underline">Limpar</a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Filtrar</button>
            </div>
        </div>
    </div>
</form>

