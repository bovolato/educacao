<x-sigem-layout title="Editar material">
    <x-page-header title="Editar material" :back-route="route('professor.materiais.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.materiais.update', $materialDidatico) }}" class="max-w-xl space-y-5">
        @csrf @method('PUT')
        <x-form-field label="Título" name="titulo" required>
            <input type="text" name="titulo" value="{{ old('titulo', $materialDidatico->titulo) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Descrição" name="descricao">
            <textarea name="descricao" rows="3" class="w-full rounded-xl border-gray-300 text-sm">{{ old('descricao', $materialDidatico->descricao) }}</textarea>
        </x-form-field>
        <x-form-field label="Link" name="link">
            <input type="url" name="link" value="{{ old('link', $materialDidatico->link) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <x-form-field label="Arquivo" name="arquivo">
            <input type="text" name="arquivo" value="{{ old('arquivo', $materialDidatico->arquivo) }}" class="w-full rounded-xl border-gray-300 text-sm">
        </x-form-field>
        <div class="flex items-center gap-2">
            <input type="hidden" name="visivel_aluno" value="0">
            <input type="checkbox" name="visivel_aluno" value="1" id="visivel_aluno" @checked(old('visivel_aluno', $materialDidatico->visivel_aluno))>
            <label for="visivel_aluno" class="text-sm text-gray-700">Visível para alunos</label>
        </div>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('professor.materiais.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
