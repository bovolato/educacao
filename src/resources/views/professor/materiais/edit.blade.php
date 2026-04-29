<x-sigem-layout title="Editar material">
    <x-page-header title="Editar material" :back-route="route('professor.materiais.index')" back-label="Lista"/>

    <form method="POST" action="{{ route('professor.materiais.update', $materialDidatico) }}" class="max-w-2xl space-y-5" enctype="multipart/form-data">
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
        <x-form-field label="Arquivo" name="arquivo" hint="Envie um novo arquivo para substituir o atual.">
            @if($materialDidatico->arquivo)
                <div class="mb-2 flex flex-wrap items-center gap-3 text-sm">
                    <a href="{{ route('professor.materiais.download', $materialDidatico) }}" class="text-indigo-600 hover:underline font-medium">
                        Baixar arquivo atual
                    </a>
                    <label class="inline-flex items-center gap-2 text-gray-700">
                        <input type="checkbox" name="remover_arquivo" value="1">
                        Remover arquivo
                    </label>
                </div>
            @endif
            <input type="file" name="arquivo" class="w-full rounded-xl border-gray-300 text-sm bg-white">
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
