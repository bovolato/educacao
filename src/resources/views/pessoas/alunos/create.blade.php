<x-sigem-layout title="Novo Aluno">

    <x-page-header title="Novo Aluno" :back-route="route('pessoas.alunos.index')" back-label="Voltar"/>

    <form method="POST" action="{{ route('pessoas.alunos.store') }}">
        @csrf

        <x-form-card title="Dados Pessoais">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome Completo" name="nome" required>
                        <input type="text" name="nome" value="{{ old('nome') }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="CPF" name="cpf">
                    <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                        class="w-full px-4 py-2.5 rounded-xl border @error('cpf') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Data de Nascimento" name="data_nascimento">
                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Sexo" name="sexo">
                    <select name="sexo" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        <option value="M" @selected(old('sexo') === 'M')>Masculino</option>
                        <option value="F" @selected(old('sexo') === 'F')>Feminino</option>
                        <option value="O" @selected(old('sexo') === 'O')>Outro</option>
                    </select>
                </x-form-field>
                <x-form-field label="Nome da Mãe" name="nome_mae">
                    <input type="text" name="nome_mae" value="{{ old('nome_mae') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Nome do Pai" name="nome_pai">
                    <input type="text" name="nome_pai" value="{{ old('nome_pai') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Naturalidade" name="naturalidade">
                    <input type="text" name="naturalidade" value="{{ old('naturalidade') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="UF de Naturalidade" name="naturalidade_uf">
                    <input type="text" name="naturalidade_uf" value="{{ old('naturalidade_uf') }}" maxlength="2" placeholder="SP"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm uppercase">
                </x-form-field>
            </div>
        </x-form-card>

        <x-form-card title="Dados do Aluno">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <x-form-field label="RA (Registro do Aluno)" name="ra">
                    <input type="text" name="ra" value="{{ old('ra') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Código do Aluno" name="codigo_aluno">
                    <input type="text" name="codigo_aluno" value="{{ old('codigo_aluno') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="NIS" name="nis">
                    <input type="text" name="nis" value="{{ old('nis') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="usa_transporte" id="usa_transporte" value="1" @checked(old('usa_transporte'))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="usa_transporte" class="text-sm text-gray-700">Utiliza Transporte Escolar</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="necessidades_especiais" id="necessidades_especiais" value="1" @checked(old('necessidades_especiais'))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="necessidades_especiais" class="text-sm text-gray-700">Possui Necessidades Especiais</label>
                </div>
                <div class="md:col-span-3">
                    <x-form-field label="Descrição das Necessidades Especiais" name="descricao_necessidades">
                        <textarea name="descricao_necessidades" rows="2"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('descricao_necessidades') }}</textarea>
                    </x-form-field>
                </div>
            </div>
        </x-form-card>

        <x-form-card title="Contato">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Celular" name="telefone">
                    <input type="text" name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="E-mail" name="email_contato">
                    <input type="email" name="email_contato" value="{{ old('email_contato') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
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
                <x-form-field label="Complemento" name="complemento">
                    <input type="text" name="complemento" value="{{ old('complemento') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Bairro" name="bairro">
                    <input type="text" name="bairro" value="{{ old('bairro') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="CEP" name="cep">
                    <input type="text" name="cep" value="{{ old('cep') }}" placeholder="00000-000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="md:col-span-2">
                    <x-form-field label="Cidade" name="cidade">
                        <input type="text" name="cidade" value="{{ old('cidade') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="UF" name="uf">
                    <input type="text" name="uf" value="{{ old('uf') }}" maxlength="2" placeholder="SP"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm uppercase">
                </x-form-field>
            </div>
        </x-form-card>

        <div class="flex items-center justify-end gap-3">
            <x-action-button href="{{ route('pessoas.alunos.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Aluno</x-action-button>
        </div>
    </form>

</x-sigem-layout>
