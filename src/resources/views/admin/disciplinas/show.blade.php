<x-sigem-layout :title="$disciplina->nome">
    <x-page-header :title="$disciplina->nome" :back-route="route('admin.disciplinas.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.disciplinas.edit', $disciplina) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Sigla</p><p class="font-medium">{{ $disciplina->sigla ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Carga Horária</p><p class="font-medium">{{ $disciplina->carga_horaria ? $disciplina->carga_horaria . 'h' : '—' }}</p></div>
            <div><p class="text-xs text-gray-500 uppercase mb-0.5">Status</p>
                <x-badge :color="$disciplina->ativo ? 'green' : 'gray'" dot>{{ $disciplina->ativo ? 'Ativa' : 'Inativa' }}</x-badge>
            </div>
            @if($disciplina->descricao)
                <div class="sm:col-span-3"><p class="text-xs text-gray-500 uppercase mb-0.5">Descrição</p><p class="text-sm text-gray-700">{{ $disciplina->descricao }}</p></div>
            @endif
        </div>
    </div>
</x-sigem-layout>
