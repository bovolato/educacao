<x-sigem-layout title="Editar Professor">
    <x-page-header :title="'Editar: ' . $professor->nome" :back-route="route('pessoas.professores.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('pessoas.professores.update', $professor) }}" x-data="professorForm()">
        @csrf @method('PATCH')
        @php
            $pessoaProf = $professor->pessoa;
            $celular = $pessoaProf ? $pessoaProf->contatos->firstWhere('tipo', 'celular') : null;
            $email   = $pessoaProf ? $pessoaProf->contatos->firstWhere('tipo', 'email') : null;
        @endphp
        <x-form-card title="Dados Pessoais">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <x-form-field label="Nome Completo" name="nome" required>
                        <input type="text" name="nome" value="{{ old('nome', $pessoaProf?->nome) }}"
                            class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </x-form-field>
                </div>
                <x-form-field label="CPF" name="cpf">
                    <input type="text" name="cpf" value="{{ old('cpf', $pessoaProf?->cpf) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Data de Nascimento" name="data_nascimento">
                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $pessoaProf?->data_nascimento?->format('Y-m-d')) }}"
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
                <x-form-field label="Cidade" name="cidade_filtro" hint="Escolha a cidade para listar apenas escolas daquela cidade.">
                    <select id="cidade_filtro" x-model="cidadeSelecionada" @change="onCidadeChange()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Todas as cidades</option>
                        @foreach($cidades as $cidade)
                            <option value="{{ $cidade }}">{{ $cidade }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <div class="md:col-span-2">
                    <x-form-field label="Escola de Atuação" name="escola_id" required hint="Escola onde o professor está administrativamente vinculado">
                        <select name="escola_id" x-model="escolaId" @change="onEscolaChange()"
                            :disabled="cidadeSelecionada && escolasFiltradas.length === 0"
                            class="w-full px-4 py-2.5 rounded-xl border @error('escola_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">{{ $cidades->isEmpty() ? 'Cadastre cidades nas escolas' : 'Selecione a escola...' }}</option>
                            <template x-for="e in escolasFiltradas" :key="e.id">
                                <option :value="String(e.id)" x-text="e.nome"></option>
                            </template>
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
    <script>
        function professorForm() {
            const escolasAll = @json($escolasJson);
            return {
                escolasAll,
                cidadeSelecionada: '',
                escolaId: @json((string) (old('escola_id', $professor->escola_id) ?? '')),
                get escolasFiltradas() {
                    if (!this.cidadeSelecionada) {
                        return escolasAll;
                    }
                    const base = escolasAll.filter(e => (e.cidade || '') === this.cidadeSelecionada);
                    if (this.escolaId && !base.some(e => String(e.id) === String(this.escolaId))) {
                        const extra = escolasAll.find(e => String(e.id) === String(this.escolaId));
                        return extra ? [...base, extra] : base;
                    }
                    return base;
                },
                onCidadeChange() {
                    const filtradas = this.cidadeSelecionada
                        ? escolasAll.filter(e => (e.cidade || '') === this.cidadeSelecionada)
                        : escolasAll;
                    if (this.escolaId && !filtradas.some(e => String(e.id) === String(this.escolaId))) {
                        this.escolaId = '';
                    }
                },
                onEscolaChange() {},
                init() {
                    if (this.escolaId) {
                        const found = escolasAll.find(e => String(e.id) === String(this.escolaId));
                        if (found && found.cidade) {
                            this.cidadeSelecionada = found.cidade;
                        }
                    }
                },
            };
        }
    </script>
</x-sigem-layout>
