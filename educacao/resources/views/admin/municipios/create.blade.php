<x-sigem-layout title="Novo Município">

    <x-page-header
        title="Novo Município"
        subtitle="Cadastrar novo município"
        :back-route="route('admin.municipios.index')"
        back-label="Voltar para Municípios"
    />

    <form method="POST" action="{{ route('admin.municipios.store') }}">
        @csrf

        <x-form-card title="Dados do Município">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome do Município" name="nome" required>
                        <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>

                <x-form-field label="UF" name="uf" required>
                    <input type="text" name="uf" value="{{ old('uf') }}" maxlength="2" placeholder="SP"
                        class="w-full px-4 py-2.5 rounded-xl border @error('uf') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm uppercase">
                </x-form-field>

                <x-form-field label="Código IBGE" name="codigo_ibge">
                    <input type="text" name="codigo_ibge" value="{{ old('codigo_ibge') }}" maxlength="10"
                        class="w-full px-4 py-2.5 rounded-xl border @error('codigo_ibge') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="CNPJ" name="cnpj">
                    <input type="text" name="cnpj" value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="Telefone" name="telefone">
                    <input type="text" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 0000-0000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>

                <x-form-field label="E-mail" name="email">
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
            </div>
        </x-form-card>

        <x-form-card title="Endereço">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Logradouro" name="logradouro">
                        <input type="text" name="logradouro" value="{{ old('logradouro') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="Número" name="numero">
                    <input type="text" name="numero" value="{{ old('numero') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Bairro" name="bairro">
                    <input type="text" name="bairro" value="{{ old('bairro') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Cidade" name="cidade">
                    <input type="text" name="cidade" value="{{ old('cidade') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="CEP" name="cep">
                    <input type="text" name="cep" value="{{ old('cep') }}" placeholder="00000-000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
            </div>
        </x-form-card>

        <div class="flex items-center justify-end gap-3">
            <x-action-button href="{{ route('admin.municipios.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'>
                Salvar Município
            </x-action-button>
        </div>
    </form>

</x-sigem-layout>
