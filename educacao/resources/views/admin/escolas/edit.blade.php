<x-sigem-layout title="Editar Escola">

    <x-page-header
        title="Editar Escola"
        :subtitle="$escola->nome"
        :back-route="route('admin.escolas.index')"
        back-label="Voltar para Escolas"
    />

    <form method="POST" action="{{ route('admin.escolas.update', $escola) }}">
        @csrf @method('PATCH')

        <x-form-card title="Dados da Escola">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome da Escola" name="nome" required>
                        <input type="text" name="nome" value="{{ old('nome', $escola->nome) }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>

                <x-form-field label="Município" name="municipio_id" required>
                    <select name="municipio_id"
                        class="w-full px-4 py-2.5 rounded-xl border @error('municipio_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id }}" @selected(old('municipio_id', $escola->municipio_id) == $municipio->id)>
                                {{ $municipio->nome }}/{{ $municipio->uf }}
                            </option>
                        @endforeach
                    </select>
                </x-form-field>

                <x-form-field label="Status" name="status" required>
                    <select name="status"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="ativa" @selected(old('status', $escola->status) === 'ativa')>Ativa</option>
                        <option value="inativa" @selected(old('status', $escola->status) === 'inativa')>Inativa</option>
                        <option value="em_obras" @selected(old('status', $escola->status) === 'em_obras')>Em Obras</option>
                    </select>
                </x-form-field>

                <x-form-field label="Código" name="codigo">
                    <input type="text" name="codigo" value="{{ old('codigo', $escola->codigo) }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('codigo') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Código INEP" name="inep">
                    <input type="text" name="inep" value="{{ old('inep', $escola->inep) }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('inep') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="CNPJ" name="cnpj">
                    <input type="text" name="cnpj" value="{{ old('cnpj', $escola->cnpj) }}" placeholder="00.000.000/0000-00"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Nome do Diretor" name="diretor_nome">
                    <input type="text" name="diretor_nome" value="{{ old('diretor_nome', $escola->diretor_nome) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Telefone" name="telefone">
                    <input type="text" name="telefone" value="{{ old('telefone', $escola->telefone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="E-mail" name="email">
                    <input type="email" name="email" value="{{ old('email', $escola->email) }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
            </div>
        </x-form-card>

        <x-form-card title="Endereço">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Logradouro" name="logradouro">
                        <input type="text" name="logradouro" value="{{ old('logradouro', $escola->logradouro) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="Número" name="numero">
                    <input type="text" name="numero" value="{{ old('numero', $escola->numero) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Complemento" name="complemento">
                    <input type="text" name="complemento" value="{{ old('complemento', $escola->complemento) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Bairro" name="bairro">
                    <input type="text" name="bairro" value="{{ old('bairro', $escola->bairro) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="CEP" name="cep">
                    <input type="text" name="cep" value="{{ old('cep', $escola->cep) }}" placeholder="00000-000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="md:col-span-2">
                    <x-form-field label="Cidade" name="cidade">
                        <input type="text" name="cidade" value="{{ old('cidade', $escola->cidade) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="UF" name="uf">
                    <input type="text" name="uf" value="{{ old('uf', $escola->uf) }}" maxlength="2" placeholder="SP"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm uppercase">
                </x-form-field>
            </div>
        </x-form-card>

        <div class="flex items-center justify-end gap-3">
            <x-action-button href="{{ route('admin.escolas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'>
                Salvar Alterações
            </x-action-button>
        </div>
    </form>

</x-sigem-layout>
