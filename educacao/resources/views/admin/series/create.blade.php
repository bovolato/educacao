<x-sigem-layout title="Nova Série">
    <x-page-header title="Nova Série" :back-route="route('admin.series.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('admin.series.store') }}">
        @csrf
        <x-form-card title="Dados da Série">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Nome" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome') }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Etapa de Ensino" name="etapa_ensino_id" required>
                    <select name="etapa_ensino_id" class="w-full px-4 py-2.5 rounded-xl border @error('etapa_ensino_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($etapas as $etapa)
                            <option value="{{ $etapa->id }}" @selected(old('etapa_ensino_id') == $etapa->id)>{{ $etapa->nome }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Sigla" name="sigla">
                    <input type="text" name="sigla" value="{{ old('sigla') }}" maxlength="10"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Ordem" name="ordem">
                    <input type="number" name="ordem" value="{{ old('ordem', 1) }}" min="1"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', true))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Série Ativa</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('admin.series.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar</x-action-button>
        </div>
    </form>
</x-sigem-layout>
