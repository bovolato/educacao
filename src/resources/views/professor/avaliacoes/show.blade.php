<x-sigem-layout title="Ver avaliação">
    <x-page-header title="Avaliação" :subtitle="$avaliacao->turma?->nome"
        :back-route="route('professor.avaliacoes.index', ['turma_id' => $avaliacao->turma_id, 'disciplina_id' => $avaliacao->disciplina_id])"
        back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('professor.notas.lancar', $avaliacao) }}">Notas</x-action-button>
            <x-action-button href="{{ route('professor.avaliacoes.edit', $avaliacao) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    <div class="max-w-2xl space-y-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1">
                {{ $avaliacao->disciplina?->nome ?? '—' }}
                · {{ $avaliacao->data_avaliacao?->format('d/m/Y') ?? '—' }}
                · Bimestre: {{ $avaliacao->periodo ?? '—' }}
            </div>
            <div class="text-lg font-semibold text-gray-900">{{ $avaliacao->titulo }}</div>
            <div class="mt-2 text-sm text-gray-700">
                <span class="font-medium">Tipo:</span> {{ $avaliacao->tipo ?? '—' }}
                <span class="mx-1">·</span>
                <span class="font-medium">Valor:</span> {{ $avaliacao->valor ?? '—' }}
            </div>
            @if($avaliacao->descricao)
                <div class="mt-4 text-sm text-gray-700 whitespace-pre-line">{{ $avaliacao->descricao }}</div>
            @else
                <div class="mt-4 text-sm text-gray-500">Sem descrição.</div>
            @endif
        </div>
    </div>
</x-sigem-layout>

