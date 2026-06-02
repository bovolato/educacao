<x-sigem-layout :title="'Nova Sala — ' . $escola->nome">

    <x-page-header title="Nova Sala" :subtitle="$escola->nome"
        :back-route="route('admin.escolas.salas.index', $escola)" back-label="Voltar para Salas" />

    <form method="POST" action="{{ route('admin.escolas.salas.store', $escola) }}">
        @csrf

        <x-form-card title="Dados da Sala">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Nome da Sala" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Ex.: Sala 01, Laboratório de Informática"
                        class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Código" name="codigo">
                    <input type="text" name="codigo" value="{{ old('codigo') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Capacidade (alunos)" name="capacidade">
                    <input type="number" name="capacidade" value="{{ old('capacidade') }}" min="1" max="200"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Tipo" name="tipo">
                    <input type="text" name="tipo" value="{{ old('tipo') }}" placeholder="Ex.: Sala de Aula, Laboratório, Quadra"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
            </div>
        </x-form-card>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.escolas.salas.index', $escola) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'>
                Salvar Sala
            </x-action-button>
        </div>
    </form>

</x-sigem-layout>
