<x-sigem-layout title="Editar Turma">
    <x-page-header :title="'Editar: ' . $turma->nome" :back-route="route('academico.turmas.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('academico.turmas.update', $turma) }}"
          @if(! isset($escolaFixa)) x-data="turmaEditForm()" @endif x-cloak>
        @csrf @method('PATCH')
        <x-form-card title="Dados da Turma">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @isset($escolaFixa)
                    <input type="hidden" name="escola_id" value="{{ $escolaFixa->id }}">
                    <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <span class="text-gray-500">Escola:</span>
                        <strong>{{ $escolaFixa->nome }}</strong>
                    </div>
                @else
                <x-form-field label="Cidade" name="cidade_filtro" hint="Escolha a cidade para listar apenas escolas daquela cidade.">
                    <select id="cidade_filtro" x-model="cidadeSelecionada" @change="onCidadeChange()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione a cidade...</option>
                        @foreach($cidades as $cidade)
                            <option value="{{ $cidade }}">{{ $cidade }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Escola" name="escola_id" required>
                    <select name="escola_id" x-model="escolaId" @change="carregarSalas()" :disabled="!cidadeSelecionada && !escolaId"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">{{ $cidades->isEmpty() ? 'Cadastre cidades nas escolas' : 'Selecione a escola...' }}</option>
                        <template x-for="e in escolasFiltradas" :key="e.id">
                            <option :value="String(e.id)" x-text="e.nome"></option>
                        </template>
                    </select>
                </x-form-field>
                @endisset
                <x-form-field label="Ano Letivo" name="ano_letivo_id" required>
                    <select name="ano_letivo_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" @selected(old('ano_letivo_id', $turma->ano_letivo_id) == $ano->id)>{{ $ano->descricao }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Série" name="serie_id" required>
                    <select name="serie_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        @foreach($series as $s)
                            <option value="{{ $s->id }}" @selected(old('serie_id', $turma->serie_id) == $s->id)>{{ $s->nome }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Turno" name="turno_id" required>
                    <select name="turno_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        @foreach($turnos as $t)
                            <option value="{{ $t->id }}" @selected(old('turno_id', $turma->turno_id) == $t->id)>{{ $t->nome }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Nome da Turma" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome', $turma->nome) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Código" name="codigo">
                    <input type="text" name="codigo" value="{{ old('codigo', $turma->codigo) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Sala" name="sala_id">
                    @isset($escolaFixa)
                        <select name="sala_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option value="">Nenhuma</option>
                            @foreach($salas as $sala)
                                <option value="{{ $sala->id }}" @selected(old('sala_id', $turma->sala_id) == $sala->id)>{{ $sala->nome }}@if($sala->capacidade) ({{ $sala->capacidade }} lugares) @endif</option>
                            @endforeach
                        </select>
                    @else
                    <select name="sala_id" x-model="salaId"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Nenhuma</option>
                        <template x-for="sala in salas" :key="sala.id">
                            <option :value="sala.id" x-text="sala.nome + (sala.capacidade ? ' (' + sala.capacidade + ' lugares)' : '')" :selected="sala.id == salaId"></option>
                        </template>
                    </select>
                    @endisset
                </x-form-field>
                <x-form-field label="Capacidade" name="capacidade">
                    <input type="number" name="capacidade" value="{{ old('capacidade', $turma->capacidade) }}" min="1" max="50"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <div class="md:col-span-2">
                    <label class="inline-flex items-start gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3">
                        <input type="checkbox" name="polivalente" value="1" class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(old('polivalente', $turma->polivalente))>
                        <span class="text-sm text-gray-800">
                            <span class="font-medium">Turma polivalente</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Lançamento único de aula e frequência por data (sem separar por disciplina).</span>
                        </span>
                    </label>
                </div>
                <x-form-field label="Status" name="status" required>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="ativa" @selected(old('status', $turma->status) === 'ativa')>Ativa</option>
                        <option value="encerrada" @selected(old('status', $turma->status) === 'encerrada')>Encerrada</option>
                        <option value="suspensa" @selected(old('status', $turma->status) === 'suspensa')>Suspensa</option>
                    </select>
                </x-form-field>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('academico.turmas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Alterações</x-action-button>
        </div>
    </form>

    @if(! isset($escolaFixa))
    @php
        $escolasJson = $escolas->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'cidade' => $e->cidade])->values();
    @endphp
    <script>
        function turmaEditForm() {
            const escolasAll = @json($escolasJson);
            return {
                escolasAll,
                cidadeSelecionada: '',
                escolaId: @json((string) (old('escola_id', $turma->escola_id) ?? '')),
                salaId: @json((string) (old('sala_id', $turma->sala_id) ?? '')),
                salas: @json($salas->map(fn($s) => ['id' => $s->id, 'nome' => $s->nome, 'capacidade' => $s->capacidade])),
                get escolasFiltradas() {
                    if (this.cidadeSelecionada) {
                        const base = this.escolasAll.filter(e => (e.cidade || '') === this.cidadeSelecionada);
                        if (this.escolaId && !base.some(e => String(e.id) === String(this.escolaId))) {
                            const extra = this.escolasAll.find(e => String(e.id) === String(this.escolaId));
                            return extra ? [...base, extra] : base;
                        }
                        return base;
                    }
                    if (this.escolaId) {
                        const only = this.escolasAll.find(e => String(e.id) === String(this.escolaId));
                        return only ? [only] : [];
                    }
                    return [];
                },
                onCidadeChange() {
                    this.escolaId = '';
                    this.salas = [];
                    this.salaId = '';
                },
                init() {
                    if (this.escolaId) {
                        const found = escolasAll.find(e => String(e.id) === String(this.escolaId));
                        if (found && found.cidade) {
                            this.cidadeSelecionada = found.cidade;
                        }
                    }
                },
                async carregarSalas() {
                    const salaAnterior = this.salaId;
                    if (!this.escolaId) {
                        this.salas = [];
                        this.salaId = '';
                        return;
                    }
                    try {
                        const resp = await fetch(`/api/escolas/${this.escolaId}/salas`);
                        this.salas = await resp.json();
                        if (salaAnterior && this.salas.some(s => String(s.id) === String(salaAnterior))) {
                            this.salaId = salaAnterior;
                        } else {
                            this.salaId = '';
                        }
                    } catch (e) { console.error(e); }
                }
            };
        }
    </script>
    @endif
</x-sigem-layout>
