<x-sigem-layout title="Novo Ano Letivo">
    <x-page-header title="Novo Ano Letivo" :back-route="route('admin.anos-letivos.index')" back-label="Voltar"/>

    <form method="POST" action="{{ route('admin.anos-letivos.store') }}">
        @csrf
        <x-form-card title="Dados do Ano Letivo">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Município" name="municipio_id" required>
                    <select name="municipio_id" class="w-full px-4 py-2.5 rounded-xl border @error('municipio_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($municipios as $m)
                            <option value="{{ $m->id }}" @selected(old('municipio_id') == $m->id)>{{ $m->nome }}/{{ $m->uf }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Descrição (Ano)" name="descricao" required>
                    <input type="text" name="descricao" value="{{ old('descricao', date('Y')) }}" placeholder="2026"
                        class="w-full px-4 py-2.5 rounded-xl border @error('descricao') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Data de Início" name="data_inicio" required>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio') }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('data_inicio') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Data de Término" name="data_fim" required>
                    <input type="date" name="data_fim" value="{{ old('data_fim') }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('data_fim') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo'))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Definir como Ano Letivo Ativo</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.anos-letivos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
