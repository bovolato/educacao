<x-sigem-layout title="Editar anotação">
    <x-page-header title="Editar anotação" :back-route="route('professor.anotacoes.show', $anotacao)" back-label="Voltar"/>

    <form method="POST" action="{{ route('professor.anotacoes.update', $anotacao) }}" class="max-w-2xl space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
            <div class="text-xs text-gray-500">
                Turma: {{ $anotacao->turma?->nome ?? '—' }}
                · Aluno: {{ $anotacao->matricula?->aluno?->pessoa?->nome ?? '—' }}
                · Bimestre: {{ $anotacao->periodo }}
            </div>
        </div>

        <x-form-field label="Assunto" name="assunto" required>
            <input type="text" name="assunto" value="{{ old('assunto', $anotacao->assunto) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>

        <x-form-field label="Texto" name="texto" required>
            <textarea name="texto" rows="7" class="w-full rounded-xl border-gray-300 text-sm">{{ old('texto', $anotacao->texto) }}</textarea>
        </x-form-field>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.anotacoes.show', $anotacao) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>

