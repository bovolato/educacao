<x-sigem-layout title="Nova tarefa">
    <x-page-header title="Nova tarefa" :back-route="route('professor.tarefas.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.tarefas.store') }}" class="max-w-xl space-y-5">
        @csrf
        <input type="hidden" name="turma_id" value="{{ $turma_id }}">
        <input type="hidden" name="disciplina_id" value="{{ $disciplina_id }}">
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Descrição" name="descricao">
            <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao') }}</textarea>
        </x-form-field>
        <x-form-field label="Data postagem" name="data_postagem">
            <input type="date" name="data_postagem" value="{{ old('data_postagem', now()->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Data entrega" name="data_entrega">
            <input type="date" name="data_entrega" value="{{ old('data_entrega') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Valor (pontos)" name="valor">
            <input type="number" step="0.01" name="valor" value="{{ old('valor') }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.tarefas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
