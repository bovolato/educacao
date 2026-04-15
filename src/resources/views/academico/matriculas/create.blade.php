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
                        @foreach($alunos as $aluno)
                            <option value="{{ $aluno->id }}" @selected(old('aluno_id', $alunoPreSelecionado) == $aluno->id)>
                                {{ $aluno->pessoa->nome }} (RA: {{ $aluno->ra ?? 'S/RA' }})
                            </option>
                        @endforeach
                    </select>
                </x-form-field>

                <x-form-field label="Escola" name="escola_id" required>
                    <select name="escola_id" x-model="escolaId" @change="carregarTurmas()"
                        class="w-full px-4 py-2.5 rounded-xl border @error('escola_id') border-red-400 @else border-gray-300 @enderror focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="">Selecione a escola...</option>
                        @foreach($escolas as $e)
                            <option value="{{ $e->id }}" @selected(old('escola_id') == $e->id)>{{ $e->nome }}</option>
                        @endforeach
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
            return {
                alunoId: '{{ old("aluno_id", $alunoPreSelecionado) }}',
                escolaId: '{{ old("escola_id") }}',
                turmaId: '{{ old("turma_id") }}',
                turmas: [],
                init() {
                    if (this.escolaId) this.carregarTurmas();
                },
                async carregarTurmas() {
                    this.turmas = [];
                    this.turmaId = '';
                    if (!this.escolaId) return;
                    try {
                        const resp = await fetch(`/api/escolas/${this.escolaId}/turmas`);
                        this.turmas = await resp.json();
                    } catch (e) { console.error(e); }
                }
            };
        }
    </script>
</x-sigem-layout>
