<x-sigem-layout title="Material">
    <x-page-header title="Material didático" :subtitle="$materialDidatico->turma?->nome"
        :back-route="route('professor.materiais.index')" back-label="Lista">
        <x-slot name="actions">
            <x-action-button href="{{ route('professor.materiais.edit', $materialDidatico) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-2xl space-y-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1">
                {{ $materialDidatico->disciplina?->nome ?? '—' }}
                · Bimestre: {{ $materialDidatico->periodo ?? '—' }}
                · {{ $materialDidatico->created_at?->format('d/m/Y H:i') ?? '—' }}
            </div>
            <div class="text-lg font-semibold text-gray-900">{{ $materialDidatico->titulo }}</div>

            @if($materialDidatico->descricao)
                <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $materialDidatico->descricao }}</div>
            @else
                <div class="mt-3 text-sm text-gray-500">Sem descrição.</div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
            <div class="text-sm font-semibold text-gray-900 mb-3">Acessos</div>
            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-gray-500">Visível para alunos:</span>
                    <span class="font-medium text-gray-900">{{ $materialDidatico->visivel_aluno ? 'Sim' : 'Não' }}</span>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if($materialDidatico->link)
                        <a href="{{ $materialDidatico->link }}" target="_blank" class="text-indigo-600 hover:underline font-medium">
                            Abrir link
                        </a>
                    @endif
                    @if($materialDidatico->arquivo)
                        <a href="{{ route('professor.materiais.download', $materialDidatico) }}" class="text-indigo-600 hover:underline font-medium">
                            Baixar arquivo
                        </a>
                    @endif
                    @if(! $materialDidatico->link && ! $materialDidatico->arquivo)
                        <span class="text-gray-500">Nenhum link/arquivo anexado.</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <form action="{{ route('professor.materiais.destroy', $materialDidatico) }}" method="POST" onsubmit="return confirm('Remover este material?');">
                @csrf
                @method('DELETE')
                <x-action-button type="submit" variant="danger">Excluir</x-action-button>
            </form>
        </div>
    </div>
</x-sigem-layout>

