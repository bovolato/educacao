<x-sigem-layout title="Conteúdo da aula">
    <x-page-header title="Conteúdo ministrado" :subtitle="$aula->data_aula?->format('d/m/Y')"
        :back-route="$aula->turma?->polivalente
            ? route('professor.aulas.index', ['turma_id' => $aula->turma_id])
            : route('professor.aulas.index', ['turma_id' => $aula->turma_id, 'disciplina_id' => $aula->disciplina_id])"
        back-label="Voltar"/>

    <x-professor-context-bar
        :turma-nome="$aula->turma?->nome"
        :disciplina-nome="$aula->turma?->polivalente ? null : ($aula->disciplina?->nome ?? null)"
        :polivalente="(bool) ($aula->turma?->polivalente ?? false)"
        :data-label="'Aula: ' . ($aula->data_aula?->format('d/m/Y') ?? '—')"
    />

    <form method="POST" action="{{ route('professor.aulas.conteudo.salvar', $aula) }}" class="max-w-2xl space-y-5">
        @csrf
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo', $conteudo?->titulo) }}"
                placeholder="Ex.: Língua Portuguesa — leitura e interpretação"
                class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Descrição" name="descricao">
            <textarea name="descricao" rows="4" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao', $conteudo?->descricao) }}</textarea>
        </x-form-field>
        <x-form-field label="Material utilizado" name="material_utilizado">
            <textarea name="material_utilizado" rows="2" class="w-full rounded-xl border-gray-300 text-sm">{{ old('material_utilizado', $conteudo?->material_utilizado) }}</textarea>
        </x-form-field>
        <div class="flex items-center gap-2">
            <input type="hidden" name="tarefa_passada" value="0">
            <input type="checkbox" name="tarefa_passada" value="1" id="tarefa_passada" @checked(old('tarefa_passada', $conteudo?->tarefa_passada))>
            <label for="tarefa_passada" class="text-sm text-gray-700">Tarefa passada</label>
        </div>
        <div class="flex justify-end">
            <x-action-button type="submit">Salvar conteúdo</x-action-button>
        </div>
    </form>
</x-sigem-layout>
