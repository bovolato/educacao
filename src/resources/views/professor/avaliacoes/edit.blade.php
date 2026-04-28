<x-sigem-layout title="Editar avaliação">
    <x-page-header title="Editar avaliação" :back-route="route('professor.avaliacoes.index', ['turma_id' => $avaliacao->turma_id, 'disciplina_id' => $avaliacao->disciplina_id])" back-label="Voltar"/>

    <form method="POST" action="{{ route('professor.avaliacoes.update', $avaliacao) }}" class="max-w-2xl space-y-5">
        @csrf @method('PUT')
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo', $avaliacao->titulo) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Tipo" name="tipo">
            <input type="text" name="tipo" value="{{ old('tipo', $avaliacao->tipo) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Data" name="data_avaliacao">
            <input type="date" name="data_avaliacao" value="{{ old('data_avaliacao', $avaliacao->data_avaliacao?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Valor máximo" name="valor">
            <input type="number" step="0.01" name="valor" value="{{ old('valor', $avaliacao->valor) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Período" name="periodo">
            <input type="text" name="periodo" value="{{ old('periodo', $avaliacao->periodo) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Descrição" name="descricao">
            <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao', $avaliacao->descricao) }}</textarea>
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.avaliacoes.index', ['turma_id' => $avaliacao->turma_id, 'disciplina_id' => $avaliacao->disciplina_id]) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
