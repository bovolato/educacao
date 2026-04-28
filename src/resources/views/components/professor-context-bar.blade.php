@props([
    'turmaNome' => null,
    'disciplinaNome' => null,
    'escolaNome' => null,
    'dataLabel' => null,
    'polivalente' => false,
])

<div class="mb-4 rounded-2xl border border-indigo-100 bg-indigo-50/80 px-4 py-3">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="text-sm text-indigo-950">
            <span class="font-semibold">Contexto:</span>
            @if($turmaNome)
                <span>{{ $turmaNome }}</span>
            @endif
            @if($polivalente)
                <span class="mx-1">·</span><span class="font-medium">Polivalente</span>
            @elseif($disciplinaNome)
                <span class="mx-1">·</span><span>{{ $disciplinaNome }}</span>
            @endif
            @if($escolaNome)
                <span class="mx-1">·</span><span class="text-indigo-900/80">{{ $escolaNome }}</span>
            @endif
            @if($dataLabel)
                <span class="mx-1">·</span><span>{{ $dataLabel }}</span>
            @endif
        </div>
        <a href="{{ route('professor.turmas.index') }}" class="text-xs text-indigo-700 hover:underline font-medium">Trocar turma</a>
    </div>
</div>

