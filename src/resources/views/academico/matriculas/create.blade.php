<x-sigem-layout title="Nova Matrícula">
    <x-page-header title="Nova Matrícula" :back-route="route('academico.matriculas.index')" back-label="Voltar"/>
    <form method="POST" action="{{ route('academico.matriculas.store') }}"
          x-data="matriculaForm()" x-cloak>
        @csrf
        <x-form-card title="Dados da Matrícula">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-form-field label="Aluno" name="aluno_id" required>
                    <select name="aluno_id" x-model="alunoId"
                        class="w-full px-4 py-2.5 rounded-xl border @error('aluno_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione o aluno...</option>
                        <template x-for="al in alunosFiltrados" :key="al.id">
                            <option :value="String(al.id)" x-text="al.nome + ' (RA: ' + (al.ra || 'S/RA') + ')'"></option>
                        </template>
                    </select>
                </x-form-field>

                <x-form-field label="Cidade" name="cidade_filtro" hint="Filtra alunos e escolas desta cidade. Em branco, lista todos os alunos e escolas.">
                    <select id="cidade_filtro" x-model="cidadeSelecionada" @change="onCidadeChange()"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Todas as cidades</option>
                        @foreach($cidades as $cidade)
                            <option value="{{ $cidade }}">{{ $cidade }}</option>
                        @endforeach
                    </select>
                </x-form-field>

                <x-form-field label="Escola" name="escola_id" required>
                    <select name="escola_id" x-model="escolaId" @change="carregarTurmas()"
                        :disabled="cidadeSelecionada && escolasFiltradas.length === 0"
                        class="w-full px-4 py-2.5 rounded-xl border @error('escola_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">{{ $cidades->isEmpty() ? 'Cadastre cidades nas escolas' : 'Selecione a escola...' }}</option>
                        <template x-for="e in escolasFiltradas" :key="e.id">
                            <option :value="String(e.id)" x-text="e.nome"></option>
                        </template>
                    </select>
                </x-form-field>

                <x-form-field label="Ano Letivo" name="ano_letivo_id" required>
                    <select name="ano_letivo_id"
                        class="w-full px-4 py-2.5 rounded-xl border @error('ano_letivo_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione...</option>
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" @selected(old('ano_letivo_id') == $ano->id)>{{ $ano->descricao }}</option>
                        @endforeach
                    </select>
                </x-form-field>

                <x-form-field label="Turma" name="turma_id" required>
                    <select name="turma_id" x-model="turmaId"
                        class="w-full px-4 py-2.5 rounded-xl border @error('turma_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione a escola primeiro...</option>
                        <template x-for="turma in turmas" :key="turma.id">
                            <option :value="turma.id"
                                x-text="turma.nome + (turma.serie ? ' — ' + turma.serie.nome : '') + (turma.turno ? ' / ' + turma.turno.nome : '')">
                            </option>
                        </template>
                    </select>
                </x-form-field>

                <x-form-field label="Nº Matrícula" name="numero_matricula" hint="Deixe em branco para gerar automaticamente">
                    <input type="text" name="numero_matricula" value="{{ old('numero_matricula') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </x-form-field>
            </div>
        </x-form-card>
        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('academico.matriculas.index') }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit">Realizar Matrícula</x-action-button>
        </div>
    </form>

    <script>
        function matriculaForm() {
            const escolasAll = @json($escolasJson);
            const alunosAll = @json($alunosJson);
            return {
                escolasAll,
                alunosAll,
                cidadeSelecionada: '',
                alunoId: @json(old('aluno_id', $alunoPreSelecionado)),
                escolaId: @json(old('escola_id') ?? ''),
                turmaId: @json(old('turma_id') ?? ''),
                turmas: [],
                get alunosFiltrados() {
                    if (!this.cidadeSelecionada) {
                        return alunosAll;
                    }
                    return alunosAll.filter(a => (a.cidade_vinculo || '') === this.cidadeSelecionada);
                },
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
                    const filtradasEscolas = this.cidadeSelecionada
                        ? escolasAll.filter(e => (e.cidade || '') === this.cidadeSelecionada)
                        : escolasAll;
                    if (this.escolaId && !filtradasEscolas.some(e => String(e.id) === String(this.escolaId))) {
                        this.escolaId = '';
                        this.turmas = [];
                        this.turmaId = '';
                    }
                    const filtradasAlunos = this.alunosFiltrados;
                    if (this.alunoId && !filtradasAlunos.some(a => String(a.id) === String(this.alunoId))) {
                        this.alunoId = '';
                    }
                    if (this.escolaId) {
                        this.carregarTurmas();
                    }
                },
                init() {
                    if (this.escolaId) {
                        const found = escolasAll.find(e => String(e.id) === String(this.escolaId));
                        if (found && found.cidade) {
                            this.cidadeSelecionada = found.cidade;
                        }
                        this.carregarTurmas();
                    } else if (this.alunoId) {
                        const al = alunosAll.find(a => String(a.id) === String(this.alunoId));
                        if (al && al.cidade_vinculo) {
                            this.cidadeSelecionada = al.cidade_vinculo;
                        }
                    }
                },
                async carregarTurmas() {
                    const turmaAnterior = this.turmaId;
                    this.turmas = [];
                    if (!this.escolaId) {
                        this.turmaId = '';
                        return;
                    }
                    try {
                        const resp = await fetch(`/api/escolas/${this.escolaId}/turmas`);
                        this.turmas = await resp.json();
                        if (turmaAnterior && this.turmas.some(t => String(t.id) === String(turmaAnterior))) {
                            this.turmaId = turmaAnterior;
                        } else {
                            this.turmaId = '';
                        }
                    } catch (e) { console.error(e); }
                }
            };
        }
    </script>
</x-sigem-layout>
