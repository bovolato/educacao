<x-sigem-layout title="Editar Professor">
    <x-page-header :title="'Editar: ' . $professor->pessoa->nome" :back-route="route('pessoas.professores.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('pessoas.professores.update', $professor) }}">
        @csrf @method('PATCH')
        @php
            $celular = $professor->pessoa->contatos->firstWhere('tipo', 'celular');
            $email   = $professor->pessoa->contatos->firstWhere('tipo', 'email');
        @endphp
        <x-form-card title="Dados Pessoais">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome Completo" name="nome" required>
                        <input type="text" name="nome" value="{{ old('nome', $professor->pessoa->nome) }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="CPF" name="cpf">
                    <input type="text" name="cpf" value="{{ old('cpf', $professor->pessoa->cpf) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Data de Nascimento" name="data_nascimento">
                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $professor->pessoa->data_nascimento?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Celular" name="telefone">
                    <input type="text" name="telefone" value="{{ old('telefone', $celular?->valor) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="E-mail" name="email_contato">
                    <input type="email" name="email_contato" value="{{ old('email_contato', $email?->valor) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
            </div>
        </x-form-card>
        <x-form-card title="Dados Funcionais">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Escola de Atuação" name="escola_id" required hint="Escola onde o professor está administrativamente vinculado">
                        <select name="escola_id" class="w-full px-4 py-2.5 rounded-xl border @error('escola_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option value="">Selecione a escola...</option>
                            @foreach($escolas as $escola)
                                <option value="{{ $escola->id }}" @selected(old('escola_id', $professor->escola_id) == $escola->id)>{{ $escola->nome }}</option>
                            @endforeach
                        </select>
                    </x-form-field>
                </div>
                <x-form-field label="Matrícula Funcional" name="matricula_funcional">
                    <input type="text" name="matricula_funcional" value="{{ old('matricula_funcional', $professor->matricula_funcional) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Registro Profissional" name="registro_profissional">
                    <input type="text" name="registro_profissional" value="{{ old('registro_profissional', $professor->registro_profissional) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="md:col-span-2">
                    <x-form-field label="Formação Acadêmica" name="formacao">
                        <input type="text" name="formacao" value="{{ old('formacao', $professor->formacao) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="Data de Admissão" name="data_admissao">
                    <input type="date" name="data_admissao" value="{{ old('data_admissao', $professor->data_admissao?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="ativo" id="ativo" value="1" @checked(old('ativo', $professor->ativo))
                        class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <label for="ativo" class="text-sm text-gray-700">Professor Ativo</label>
                </div>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('pessoas.professores.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Alterações</x-action-button>
        </div>
    </form>
</x-sigem-layout>
