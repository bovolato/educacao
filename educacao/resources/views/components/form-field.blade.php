@props(['label', 'name', 'required' => false, 'hint' => null, 'error' => null])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1.5">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>

    {{ $slot }}

    @if($hint && !($error ?? $errors->has($name)))
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="mt-1 text-xs text-red-600">{{ $errors->first($name) }}</p>
    @endif
</div>
