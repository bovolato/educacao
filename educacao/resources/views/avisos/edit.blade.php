<x-sigem-layout title="Editar Aviso">
    <x-page-header :title="'Editar Aviso'" :back-route="route('avisos.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('avisos.update', $aviso) }}" x-data="{ tipoDestino: '{{ old('tipo_destino', $aviso->tipo_destino) }}' }">
        @csrf @method('PATCH')
        <x-form-card title="Dados do Aviso">
            <div class="grid grid-cols-1 gap-5">
                <x-form-field label="Título" name="titulo" required>
                    <input type="text" name="titulo" value="{{ old('titulo', $aviso->titulo) }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('titulo') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Mensagem" name="mensagem" required>
                    <textarea name="mensagem" rows="6"
                        class="w-full px-4 py-2.5 rounded-xl border @error('mensagem') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('mensagem', $aviso->mensagem) }}</textarea>
                </x-form-field>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form-field label="Destino" name="tipo_destino" required>
                        <select name="tipo_destino" x-model="tipoDestino"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option value="geral" @selected(old('tipo_destino', $aviso->tipo_destino) === 'geral')>Geral</option>
                            <option value="escola" @selected(old('tipo_destino', $aviso->tipo_destino) === 'escola')>Escola específica</option>
                            <option value="turma" @selected(old('tipo_destino', $aviso->tipo_destino) === 'turma')>Turma específica</option>
                        </select>
                    </x-form-field>
                    <div x-show="tipoDestino === 'escola' || tipoDestino === 'turma'">
                        <x-form-field label="Escola" name="escola_id">
                            <select name="escola_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                                <option value="">Selecione...</option>
                                @foreach($escolas as $e)
                                    <option value="{{ $e->id }}" @selected(old('escola_id', $aviso->escola_id) == $e->id)>{{ $e->nome }}</option>
                                @endforeach
                            </select>
                        </x-form-field>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', $aviso->ativo))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Aviso Ativo (visível)</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('avisos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Alterações</x-action-button>
        </div>
    </form>
</x-sigem-layout>
