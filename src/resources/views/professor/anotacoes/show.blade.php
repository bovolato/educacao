<x-sigem-layout title="Anotação">
    <x-page-header title="Anotação" :back-route="route('professor.anotacoes.index')" back-label="Lista">
        <x-slot name="actions">
            <x-action-button href="{{ route('professor.anotacoes.edit', $anotacao) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="max-w-2xl space-y-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
            <div class="text-xs text-gray-500 mb-1">
                {{ $anotacao->created_at?->format('d/m/Y H:i') ?? '—' }}
                · Turma: {{ $anotacao->turma?->nome ?? '—' }}
                · Aluno: {{ $anotacao->matricula?->aluno?->pessoa?->nome ?? '—' }}
            </div>
            <div class="text-lg font-semibold text-gray-900">{{ $anotacao->assunto }}</div>
            <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $anotacao->texto }}</div>
        </div>

        <div class="flex justify-between items-center">
            <x-action-button href="{{ route('professor.alunos.show', $anotacao->matricula_id) }}" variant="secondary">
                Abrir resumão do aluno
            </x-action-button>
            <form method="POST" action="{{ route('professor.anotacoes.destroy', $anotacao) }}" onsubmit="return confirm('Remover esta anotação?');">
                @csrf
                @method('DELETE')
                <x-action-button type="submit" variant="danger">Excluir</x-action-button>
            </form>
        </div>
    </div>
</x-sigem-layout>

