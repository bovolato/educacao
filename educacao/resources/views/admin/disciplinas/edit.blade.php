<x-sigem-layout title="Editar Disciplina">
    <x-page-header :title="'Editar: ' . $disciplina->nome" :back-route="route('admin.disciplinas.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('admin.disciplinas.update', $disciplina) }}">
        @csrf @method('PATCH')
        <x-form-card title="Dados da Disciplina">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Nome" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome', $disciplina->nome) }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Sigla" name="sigla">
                    <input type="text" name="sigla" value="{{ old('sigla', $disciplina->sigla) }}" maxlength="10"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Carga Horária (horas)" name="carga_horaria">
                    <input type="number" name="carga_horaria" value="{{ old('carga_horaria', $disciplina->carga_horaria) }}" min="1"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex items-center gap-3 mt-6">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', $disciplina->ativo))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Disciplina Ativa</label>
                </div>
                <div class="md:col-span-2">
                    <x-form-field label="Descrição" name="descricao">
                        <textarea name="descricao" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('descricao', $disciplina->descricao) }}</textarea>
                    </x-form-field>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.disciplinas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Alterações</x-action-button>
        </div>
    </form>
</x-sigem-layout>
