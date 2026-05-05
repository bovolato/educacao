<x-sigem-layout :title="$aluno->nome">

    <x-page-header :title="$aluno->nome" subtitle="Ficha do Aluno" :back-route="route('pessoas.alunos.index')" back-label="Voltar para Alunos">
        <x-slot name="actions">
            <x-action-button href="{{ route('academico.matriculas.create') }}?aluno_id={{ $aluno->id }}" variant="secondary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'>
                Nova Matrícula
            </x-action-button>
            <x-action-button href="{{ route('pessoas.alunos.edit', $aluno) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    @php
        $temMatriculaAtiva = $aluno->matriculas->where('situacao', 'ativa')->isNotEmpty();
    @endphp

    @if(!$temMatriculaAtiva)
        <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-amber-800">Este aluno não possui matrícula ativa</p>
                <p class="text-xs text-amber-600 mt-0.5">Para que o aluno fique ativo no sistema, é necessário realizar uma matrícula.
                    <a href="{{ route('academico.matriculas.create') }}?aluno_id={{ $aluno->id }}" class="underline font-medium">Matricular agora</a>
                </p>
            </div>
        </div>
    @endif

    @if(! $aluno->pessoa)
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <strong>Cadastro incompleto:</strong> não há registro de pessoa vinculado a este aluno. Os dados pessoais não podem ser exibidos. Corrija o vínculo (<code class="text-xs">pessoa_id</code>) no banco de dados.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados Pessoais</h3>
                @if($aluno->pessoa)
                    @php $ap = $aluno->pessoa; @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">CPF</p><p class="font-medium">{{ $ap->cpf ?? '—' }}</p></div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Nascimento</p>
                            <p class="font-medium">{{ $ap->data_nascimento ? \Carbon\Carbon::parse($ap->data_nascimento)->format('d/m/Y') : '—' }}</p>
                        </div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Sexo</p><p class="font-medium">{{ $ap->sexo === 'M' ? 'Masculino' : ($ap->sexo === 'F' ? 'Feminino' : '—') }}</p></div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Nome da Mãe</p><p class="font-medium">{{ $ap->nome_mae ?? '—' }}</p></div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Nome do Pai</p><p class="font-medium">{{ $ap->nome_pai ?? '—' }}</p></div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Naturalidade</p>
                            <p class="font-medium">{{ $ap->naturalidade ?? '—' }}{{ $ap->naturalidade_uf ? '/' . $ap->naturalidade_uf : '' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">—</p>
                @endif
            </div>

            @if($aluno->pessoa && $aluno->pessoa->contatos->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Contatos</h3>
                    <div class="space-y-2">
                        @foreach($aluno->pessoa->contatos as $contato)
                            <div class="flex items-center gap-3">
                                <x-badge color="blue">{{ ucfirst($contato->tipo) }}</x-badge>
                                <span class="text-sm text-gray-700">{{ $contato->valor }}</span>
                                @if($contato->principal) <x-badge color="green">Principal</x-badge> @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($aluno->pessoa && $aluno->pessoa->enderecos->isNotEmpty())
                @php $end = $aluno->pessoa->enderecos->firstWhere('principal', true) ?? $aluno->pessoa->enderecos->first(); @endphp
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Endereço</h3>
                    <p class="text-sm text-gray-700">{{ $end->logradouro }}{{ $end->numero ? ', ' . $end->numero : '' }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $end->bairro }} · {{ $end->cidade }}/{{ $end->uf }} · CEP {{ $end->cep }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Matrículas</h3>
                    <a href="{{ route('academico.matriculas.create') }}?aluno_id={{ $aluno->id }}" class="text-xs text-indigo-600 hover:underline font-medium">+ Nova Matrícula</a>
                </div>
                @if($aluno->matriculas->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhuma matrícula registrada.
                        <a href="{{ route('academico.matriculas.create') }}?aluno_id={{ $aluno->id }}" class="text-indigo-600 hover:underline">Matricular agora</a>
                    </p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($aluno->matriculas->sortByDesc('data_matricula') as $matricula)
                            <div class="px-6 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $matricula->turma?->nome ?? 'Sem turma' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $matricula->escola?->nome ?? '—' }} ·
                                        {{ $matricula->turma?->serie?->nome ?? '' }} ·
                                        {{ $matricula->data_matricula?->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @php
                                        $colors = ['ativa' => 'green', 'transferida' => 'yellow', 'evadida' => 'red', 'concluida' => 'blue', 'cancelada' => 'gray'];
                                    @endphp
                                    <x-badge :color="$colors[$matricula->situacao] ?? 'gray'" dot>{{ ucfirst($matricula->situacao) }}</x-badge>
                                    <a href="{{ route('academico.matriculas.show', $matricula) }}" class="text-xs text-indigo-600 hover:underline">Ver</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">Painel pedagógico</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Frequência e notas por bimestre (gestão).</p>
                    </div>
                    <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-xs text-gray-500">Matrícula</label>
                        <select name="matricula_id" class="rounded-xl border-gray-300 text-sm" onchange="this.form.submit()">
                            @foreach($matriculasAtivas as $m)
                                <option value="{{ $m->id }}" @selected(($matriculaSelecionada?->id ?? null) === $m->id)>
                                    {{ $m->turma?->nome ?? 'Sem turma' }} · {{ $m->escola?->nome ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                        <label class="text-xs text-gray-500 sm:ml-2">Bimestre</label>
                        <select name="periodo" class="rounded-xl border-gray-300 text-sm" onchange="this.form.submit()">
                            @foreach($periodos as $p)
                                <option value="{{ $p }}" @selected($p === $periodoSelecionado)>{{ $p }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if(! $matriculaSelecionada)
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Selecione uma matrícula para ver dados pedagógicos.</p>
                @else
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach([
                                ['Presentes', (int) ($freqResumo?->presentes ?? 0), 'green'],
                                ['Faltas', (int) ($freqResumo?->faltas ?? 0), 'red'],
                                ['Justificadas', (int) ($freqResumo?->justificadas ?? 0), 'yellow'],
                                ['Atrasos', (int) ($freqResumo?->atrasos ?? 0), 'violet'],
                            ] as [$label, $valor, $cor])
                                <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
                                    <p class="text-xs text-gray-500 uppercase mb-1">{{ $label }}</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $valor }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="rounded-2xl border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h4 class="font-semibold text-gray-800">Avaliações e notas</h4>
                                <span class="text-xs text-gray-500">{{ $avaliacoes->count() }} avaliação(ões)</span>
                            </div>
                            @if($avaliacoes->isEmpty())
                                <p class="px-5 py-8 text-center text-gray-500 text-sm">Nenhuma avaliação encontrada neste bimestre.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-[900px] w-full text-sm">
                                        <thead class="bg-gray-50 text-left text-gray-600">
                                            <tr>
                                                <th class="px-5 py-3">Data</th>
                                                <th class="px-5 py-3">Avaliação</th>
                                                <th class="px-5 py-3">Disciplina</th>
                                                <th class="px-5 py-3">Professor</th>
                                                <th class="px-5 py-3">Nota</th>
                                                <th class="px-5 py-3">Obs.</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($avaliacoes as $av)
                                                @php $n = $notasPorAvaliacao->get($av->id); @endphp
                                                <tr class="hover:bg-gray-50/80">
                                                    <td class="px-5 py-3 whitespace-nowrap">{{ $av->data_avaliacao?->format('d/m/Y') ?? '—' }}</td>
                                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $av->titulo }}</td>
                                                    <td class="px-5 py-3">{{ $av->disciplina?->nome ?? '—' }}</td>
                                                    <td class="px-5 py-3">{{ $av->professor?->pessoa?->nome ?? '—' }}</td>
                                                    <td class="px-5 py-3 whitespace-nowrap">
                                                        @if($n && $n->falta_na_avaliacao)
                                                            <x-badge color="red">Falta</x-badge>
                                                        @else
                                                            <span class="font-medium">{{ $n?->nota ?? '—' }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-5 py-3 text-gray-600">{{ $n?->observacao ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h4 class="font-semibold text-gray-800">Anotações dos professores</h4>
                                <span class="text-xs text-gray-500">{{ $anotacoesProfessor->count() }} anotação(ões)</span>
                            </div>
                            @if($anotacoesProfessor->isEmpty())
                                <p class="px-5 py-8 text-center text-gray-500 text-sm">Nenhuma anotação registrada neste bimestre.</p>
                            @else
                                <div class="divide-y divide-gray-100">
                                    @foreach($anotacoesProfessor as $an)
                                        <div class="px-5 py-4">
                                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $an->assunto }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ $an->professor?->pessoa?->nome ?? 'Professor' }}
                                                        · {{ $an->created_at?->format('d/m/Y H:i') ?? '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ $an->texto }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Status</h3>
                <x-badge :color="$aluno->ativo ? 'green' : 'gray'" dot>{{ $aluno->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
                @if(!$temMatriculaAtiva && $aluno->ativo)
                    <p class="text-xs text-amber-600 mt-2">Sem matrícula ativa</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Dados do Aluno</h3>
                <div class="space-y-3">
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Cidade (rede)</p><p class="font-medium text-sm">{{ $aluno->cidade_vinculo ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">RA</p><p class="font-mono text-sm font-medium">{{ $aluno->ra ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Código</p><p class="font-medium text-sm">{{ $aluno->codigo_aluno ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">NIS</p><p class="font-medium text-sm">{{ $aluno->nis ?? '—' }}</p></div>
                    <div class="flex gap-2 flex-wrap">
                        @if($aluno->usa_transporte) <x-badge color="blue">Transporte Escolar</x-badge> @endif
                        @if($aluno->necessidades_especiais) <x-badge color="orange">Necessidades Especiais</x-badge> @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Responsáveis</h3>
                @if($aluno->responsaveis->isEmpty())
                    <p class="text-sm text-gray-500">Nenhum responsável vinculado.</p>
                @else
                    <div class="space-y-2">
                        @foreach($aluno->responsaveis as $resp)
                            @php
                                $rp = $resp->pessoa;
                                $rcont = $rp
                                    ? ($rp->contatos->firstWhere('principal', true)
                                        ?? $rp->contatos->firstWhere('tipo', 'celular')
                                        ?? $rp->contatos->firstWhere('tipo', 'whatsapp')
                                        ?? $rp->contatos->firstWhere('tipo', 'fixo'))
                                    : null;
                            @endphp
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $resp->nome }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $resp->pivot->grau_parentesco ?? $resp->tipo_responsavel ?? 'Tel.: ' }}
                                    @if($rcont?->valor)
                                        · {{ $rcont->valor }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-sigem-layout>
