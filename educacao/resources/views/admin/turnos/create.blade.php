<x-sigem-layout title="Novo Turno">
    <x-page-header title="Novo Turno" :back-route="route('admin.turnos.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('admin.turnos.store') }}">
        @csrf
        <x-form-card title="Dados do Turno">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <x-form-field label="Nome" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Ex.: Manhã"
                        class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Hora Início" name="hora_inicio">
                    <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Hora Fim" name="hora_fim">
                    <input type="time" name="hora_fim" value="{{ old('hora_fim') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', true))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Turno Ativo</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.turnos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
