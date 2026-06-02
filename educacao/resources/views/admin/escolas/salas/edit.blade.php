<x-sigem-layout :title="'Editar Sala — ' . $sala->nome">

    <x-page-header :title="'Editar: ' . $sala->nome" :subtitle="$sala->escola->nome"
        :back-route="route('admin.escolas.salas.index', $sala->escola_id)" back-label="Voltar para Salas" />

    <form method="POST" action="{{ route('admin.escolas.salas.update', $sala) }}">
        @csrf @method('PATCH')

        <x-form-card title="Dados da Sala">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Nome da Sala" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome', $sala->nome) }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Código" name="codigo">
                    <input type="text" name="codigo" value="{{ old('codigo', $sala->codigo) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Capacidade (alunos)" name="capacidade">
                    <input type="number" name="capacidade" value="{{ old('capacidade', $sala->capacidade) }}" min="1" max="200"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Tipo" name="tipo">
                    <input type="text" name="tipo" value="{{ old('tipo', $sala->tipo) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Status" name="ativo">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="ativo" value="0">
                        <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $sala->ativo))
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Sala ativa</span>
                    </label>
                </x-form-field>
            </div>
        </x-form-card>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.escolas.salas.index', $sala->escola_id) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'>
                Salvar Alterações
            </x-action-button>
        </div>
    </form>

</x-sigem-layout>
