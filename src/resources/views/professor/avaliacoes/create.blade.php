<x-sigem-layout title="Nova avaliação">
    <x-page-header title="Nova avaliação" :back-route="route('professor.avaliacoes.index', ['turma_id' => $turma_id, 'disciplina_id' => $disciplina_id])" back-label="Voltar"/>

    <form method="POST" action="{{ route('professor.avaliacoes.store') }}" class="max-w-lg space-y-5">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma_id }}">
        <input type="hidden" name="disciplina_id" value="{{ $disciplina_id }}">
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Tipo" name="tipo">
            <input type="text" name="tipo" value="{{ old('tipo', 'prova') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Data" name="data_avaliacao">
            <input type="date" name="data_avaliacao" value="{{ old('data_avaliacao', now()->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Valor máximo" name="valor">
            <input type="number" step="0.01" name="valor" value="{{ old('valor', '10') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Bimestre (global)" name="periodo">
            <input type="text" value="{{ session('professor_periodo', '1B') }}" class="w-full rounded-xl border-gray-300 text-sm bg-gray-50" readonly>
        </x-form-field>
        <x-form-field label="Descrição" name="descricao">
            <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao') }}</textarea>
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.avaliacoes.index', ['turma_id' => $turma_id, 'disciplina_id' => $disciplina_id]) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
