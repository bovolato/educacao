@props([
    'vinculos',
    'turmaId' => null,
    'disciplinaId' => null,
    /** frequencias | notas_bimestre | avaliacoes | aulas | alunos */
    'active' => 'frequencias',
    /** Rota do índice do módulo atual (para o select redirecionar mantendo o contexto) */
    'moduleRoute' => 'professor.frequencias.index',
])

@php
    $tid = $turmaId !== null && $turmaId !== '' ? (int) $turmaId : null;
    $did = $disciplinaId !== null && $disciplinaId !== '' ? (int) $disciplinaId : null;
    $periodoAtual = session('professor_periodo', '1B');
    $turmaPolivalente = false;
    if ($tid && $vinculos) {
        foreach ($vinculos as $vv) {
            if ((int) $vv->turma_id === $tid && (bool) ($vv->turma_polivalente ?? false)) {
                $turmaPolivalente = true;
                break;
            }
        }
    }

    // Se a turma é polivalente, ignora disciplina_id (mesmo que venha na URL vindo de outro módulo).
    if ($turmaPolivalente) {
        $did = null;
    }

    $turmaPolivalenteSelecionada = (bool) ($tid && $turmaPolivalente);

    $query = $tid ? array_filter(['turma_id' => $tid, 'disciplina_id' => $did]) : [];
    $queryDisciplina = ($tid && $did) ? ['turma_id' => $tid, 'disciplina_id' => $did] : null;

    $rotuloContexto = null;
    if ($tid && $vinculos) {
        foreach ($vinculos as $vv) {
            if ((int) $vv->turma_id !== $tid) continue;
            if ($did && (int) $vv->disciplina_id === $did) {
                $rotuloContexto = $vv->turma_nome.' · '.$vv->disciplina_nome;
                break;
            }
            if (! $did && (bool) ($vv->turma_polivalente ?? false)) {
                $rotuloContexto = $vv->turma_nome.' · Polivalente';
                break;
            }
        }
    }

    $tabClass = function (string $key) use ($active) {
        $base = 'inline-flex items-center px-3 py-2 text-sm font-medium rounded-t-lg border-b-2 transition-colors';
        if ($active === $key) {
            return $base.' border-indigo-600 text-indigo-700 bg-white';
        }
        return $base.' border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-200';
    };

    // Em módulos "por turma" (ex.: polivalente), queremos travar o contexto em "Turma (polivalente)".
    // Assim, evitamos o usuário selecionar disciplina numa turma polivalente quando o módulo não usa disciplina.
    $travarPolivalente = in_array($active, ['frequencias', 'notas_bimestre'], true);
@endphp

<div class="mb-6 space-y-4">
    <div>
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-3">
            <div class="text-xs text-gray-500">
                <span class="font-medium text-gray-700">Bimestre atual:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-800">{{ $periodoAtual }}</span>
                <span class="ml-1">— tudo que você cria/consulta é filtrado por esse bimestre.</span>
            </div>
            <form method="POST" action="{{ route('professor.contexto.periodo') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                <label class="text-xs text-gray-500">Trocar bimestre</label>
                <select name="periodo" class="rounded-xl border-gray-300 text-sm" onchange="this.form.submit()">
                    @foreach(['1B','2B','3B','4B'] as $p)
                        <option value="{{ $p }}" @selected($p === $periodoAtual)>{{ $p }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Turma e disciplina</label>
        <select class="rounded-xl border-gray-300 text-sm max-w-xl w-full shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            onchange="const raw=this.value; if(!raw) return; if(raw.includes('|P')) { const tid=raw.split('|')[0]; location='{{ route($moduleRoute) }}?turma_id='+tid; return; } const v=raw.split(':'); if(v[0]) location='{{ route($moduleRoute) }}?turma_id='+v[0]+'&disciplina_id='+v[1];">
            <option value="">Selecione a turma (depois a disciplina)…</option>
            @foreach(collect($vinculos)->groupBy('turma_id') as $grupoTurma)
                @php $cab = $grupoTurma->first(); @endphp
                <optgroup label="{{ $cab->turma_nome }}{{ isset($cab->escola_nome) && $cab->escola_nome ? ' · '.$cab->escola_nome : '' }}">
                    @if((bool) ($cab->turma_polivalente ?? false))
                        <option value="{{ $cab->turma_id }}|P" @selected($tid === (int) $cab->turma_id && $turmaPolivalenteSelecionada)>
                            Turma (polivalente)
                        </option>
                    @endif
                    @if(!((bool) ($cab->turma_polivalente ?? false) && $travarPolivalente))
                        @foreach($grupoTurma as $v)
                            <option value="{{ $v->turma_id }}:{{ $v->disciplina_id }}" @selected($tid === (int) $v->turma_id && $did === (int) $v->disciplina_id)>
                                {{ $v->disciplina_nome }}
                            </option>
                        @endforeach
                    @endif
                </optgroup>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">É uma turma só; cada <strong>disciplina</strong> tem diário próprio (frequência e notas separadas por matéria).</p>
    </div>

    @if($rotuloContexto)
        <div class="rounded-xl border border-indigo-100 bg-indigo-50/80 px-4 py-3 text-sm text-indigo-950">
            <span class="font-medium">Contexto:</span> {{ $rotuloContexto }}
        </div>
    @endif

    <div class="flex flex-wrap gap-1 border-b border-gray-200 bg-gray-50/80 rounded-t-xl px-2 pt-2">
        <a href="{{ route('professor.frequencias.index', $query) }}" class="{{ $tabClass('frequencias') }}">Frequência</a>
        <a href="{{ route('professor.notas-bimestre.index', $query) }}" class="{{ $tabClass('notas_bimestre') }}">Notas (bimestre)</a>
        <a href="{{ route('professor.avaliacoes.index', $query) }}" class="{{ $tabClass('avaliacoes') }}">Avaliações</a>
        <a href="{{ route('professor.aulas.index', $query) }}" class="{{ $tabClass('aulas') }}">Aulas / conteúdo</a>
        <a href="{{ route('professor.alunos.index', $query) }}" class="{{ $tabClass('alunos') }}">Alunos</a>
        @if($queryDisciplina)
            @php $tabExtra = 'inline-flex items-center px-3 py-2 text-sm font-medium rounded-t-lg border-b-2 border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-200'; @endphp
            <a href="{{ route('professor.planos-ensino.create', $queryDisciplina) }}" class="{{ $tabExtra }}" title="Novo plano de ensino">+ Plano ensino</a>
            <a href="{{ route('professor.planos.create', $queryDisciplina) }}" class="{{ $tabExtra }}" title="Novo plano de aula">+ Plano aula</a>
            <a href="{{ route('professor.materiais.create', $queryDisciplina) }}" class="{{ $tabExtra }}">+ Material</a>
            <a href="{{ route('professor.tarefas.create', $queryDisciplina) }}" class="{{ $tabExtra }}">+ Tarefa</a>
        @else
            <span class="inline-flex items-center px-3 py-2 text-xs text-gray-400" title="Selecione turma e disciplina">Plano ensino · …</span>
        @endif
    </div>
</div>

<div class="space-y-5">
    {{ $slot }}
</div>
