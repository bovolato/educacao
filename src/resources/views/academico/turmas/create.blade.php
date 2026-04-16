<x-sigem-layout title="Nova Turma">
    <x-page-header title="Nova Turma" :back-route="route('academico.turmas.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('academico.turmas.store') }}"
          x-data="turmaForm()" x-cloak>
        @csrf
        <x-form-card title="Dados da Turma">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Cidade" name="cidade_filtro" hint="Escolha primeiro a cidade; em seguida aparecerão apenas as escolas daquela cidade.">
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
                        class="w-full px-4 py-2.5 rounded-xl border @error('escola_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">{{ $cidades->isEmpty() ? 'Cadastre cidades nas escolas' : 'Selecione a escola...' }}</option>
                        <template x-for="e in escolasFiltradas" :key="e.id">
                            <option :value="String(e.id)" x-text="e.nome"></option>
                        </template>
                    </select>
                </x-form-field>
                <x-form-field label="Ano Letivo" name="ano_letivo_id" required>
                    <select name="ano_letivo_id" class="w-full px-4 py-2.5 rounded-xl border @error('ano_letivo_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" @selected(old('ano_letivo_id') == $ano->id)>{{ $ano->descricao }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Série" name="serie_id" required>
                    <select name="serie_id" class="w-full px-4 py-2.5 rounded-xl border @error('serie_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($series as $s)
                            <option value="{{ $s->id }}" @selected(old('serie_id') == $s->id)>{{ $s->nome }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Turno" name="turno_id" required>
                    <select name="turno_id" class="w-full px-4 py-2.5 rounded-xl border @error('turno_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($turnos as $t)
                            <option value="{{ $t->id }}" @selected(old('turno_id') == $t->id)>{{ $t->nome }}</option>
                        @endforeach
                    </select>
                </x-form-field>
                <x-form-field label="Nome da Turma" name="nome" required>
                    <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Ex.: 5º Ano A"
                        class="w-full px-4 py-2.5 rounded-xl border @error('nome') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Código" name="codigo">
                    <input type="text" name="codigo" value="{{ old('codigo') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Sala" name="sala_id">
                    <select name="sala_id" x-model="salaId"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Nenhuma</option>
                        <template x-for="sala in salas" :key="sala.id">
                            <option :value="sala.id" x-text="sala.nome + (sala.capacidade ? ' (' + sala.capacidade + ' lugares)' : '')"></option>
                        </template>
                    </select>
                    <template x-if="escolaId && salasCarregadas && salas.length === 0">
                        <p class="mt-1.5 text-xs text-amber-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <span>Esta escola não possui salas cadastradas.
                                <a :href="'/admin/escolas/' + escolaId + '/salas/create'" class="underline font-medium" target="_blank">Cadastrar sala agora</a>
                            </span>
                        </p>
                    </template>
                </x-form-field>
                <x-form-field label="Capacidade (alunos)" name="capacidade">
                    <input type="number" name="capacidade" value="{{ old('capacidade', 35) }}" min="1" max="50"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
                <x-form-field label="Status" name="status" required>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="ativa" @selected(old('status', 'ativa') === 'ativa')>Ativa</option>
                        <option value="encerrada" @selected(old('status') === 'encerrada')>Encerrada</option>
                        <option value="suspensa" @selected(old('status') === 'suspensa')>Suspensa</option>
                    </select>
                </x-form-field>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('academico.turmas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Salvar Turma</x-action-button>
        </div>
    </form>

    @php
        $escolasJson = $escolas->map(fn ($e) => ['id' => (int) $e->id, 'nome' => $e->nome, 'cidade' => $e->cidade])->values();
    @endphp
    <script>
        function turmaForm() {
            const escolasAll = @json($escolasJson);
            return {
                escolasAll,
                cidadeSelecionada: '',
                escolaId: @json((string) (old('escola_id', request('escola_id')) ?? '')),
                salaId: @json((string) (old('sala_id') ?? '')),
                salas: [],
                salasCarregadas: false,
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
                    this.salasCarregadas = false;
                },
                init() {
                    if (this.escolaId) {
                        const found = escolasAll.find(e => String(e.id) === String(this.escolaId));
                        if (found && found.cidade) {
                            this.cidadeSelecionada = found.cidade;
                        }
                    }
                    if (this.escolaId) this.carregarSalas();
                },
                async carregarSalas() {
                    const salaAnterior = this.salaId;
                    this.salas = [];
                    this.salasCarregadas = false;
                    if (!this.escolaId) {
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
                    this.salasCarregadas = true;
                }
            };
        }
    </script>
</x-sigem-layout>
