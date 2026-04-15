<x-sigem-layout title="Novo Aviso">
    <x-page-header title="Novo Aviso" :back-route="route('avisos.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('avisos.store') }}" x-data="{ tipoDestino: '{{ old('tipo_destino', 'geral') }}' }">
        @csrf
        <x-form-card title="Dados do Aviso">
            <div class="grid grid-cols-1 gap-5">
                <x-form-field label="Título" name="titulo" required>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" placeholder="Título do aviso ou comunicado"
                        class="w-full px-4 py-2.5 rounded-xl border @error('titulo') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Mensagem" name="mensagem" required>
                    <textarea name="mensagem" rows="6" placeholder="Digite o conteúdo do aviso..."
                        class="w-full px-4 py-2.5 rounded-xl border @error('mensagem') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('mensagem') }}</textarea>
                </x-form-field>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-form-field label="Destino" name="tipo_destino" required>
                        <select name="tipo_destino" x-model="tipoDestino"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option value="geral">Geral (toda a rede)</option>
                            <option value="escola">Escola específica</option>
                            <option value="turma">Turma específica</option>
                        </select>
                    </x-form-field>

                    <div x-show="tipoDestino === 'escola' || tipoDestino === 'turma'">
                        <x-form-field label="Escola" name="escola_id">
                            <select name="escola_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                                <option value="">Selecione a escola...</option>
                                @foreach($escolas as $e)
                                    <option value="{{ $e->id }}" @selected(old('escola_id') == $e->id)>{{ $e->nome }}</option>
                                @endforeach
                            </select>
                        </x-form-field>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', true))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Publicar aviso imediatamente</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('avisos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>'>
                Publicar Aviso
            </x-action-button>
        </div>
    </form>
</x-sigem-layout>
