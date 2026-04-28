<x-sigem-layout :title="'Aluno: ' . ($matricula->aluno?->nome ?? 'Detalhes')">
    <x-page-header
        :title="$matricula->aluno?->nome ?? 'Aluno'"
        :subtitle="($turma?->nome ?? '') . ' · resumão (todas as disciplinas)'"
        :back-route="route('professor.alunos.index', array_filter(['turma_id' => $turma->id]))"
        back-label="Voltar"
    />

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-1 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="text-sm text-gray-500 mb-1">Informações</div>
                <div class="text-gray-900 font-semibold text-lg">{{ $matricula->aluno?->nome ?? '—' }}</div>
                <div class="mt-3 space-y-1 text-sm text-gray-700">
                    <div><span class="text-gray-500">RA:</span> {{ $matricula->aluno?->ra ?? '—' }}</div>
                    <div><span class="text-gray-500">Matrícula:</span> {{ $matricula->numero_matricula ?? $matricula->id }}</div>
                    <div><span class="text-gray-500">Turma:</span> {{ $turma->nome ?? '—' }}</div>
                    <div><span class="text-gray-500">Escola:</span> {{ $turma?->escola?->nome ?? '—' }}</div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="text-sm text-gray-500 mb-2">Responsáveis</div>
                @php $resp = $matricula->aluno?->responsaveis ?? collect(); @endphp
                @if($resp->isEmpty())
                    <div class="text-sm text-gray-500">Sem responsáveis cadastrados.</div>
                @else
                    <div class="space-y-2">
                        @foreach($resp as $r)
                            <div class="rounded-xl bg-gray-50 px-3 py-2">
                                <div class="text-sm font-medium text-gray-900">{{ $r->pessoa?->nome_exibicao ?? '—' }}</div>
                                @if($r->pivot?->grau_parentesco)
                                    <div class="text-xs text-gray-600">{{ $r->pivot->grau_parentesco }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50/80 p-4">
                <div class="text-sm text-indigo-950">
                    <span class="font-semibold">Contexto:</span>
                    {{ $turma->nome ?? '' }}
                    @if($turma->polivalente)
                        <span class="mx-1">·</span><span class="font-medium">Polivalente (frequência por turma)</span>
                    @endif
                </div>
                <div class="mt-2 text-xs text-indigo-900/80">
                    Este painel reúne: frequência, avaliações/notas, tarefas e recados vinculados à turma.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                    <div class="text-xs text-gray-500">Presenças</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ (int) ($freqResumo->presentes ?? 0) }}</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                    <div class="text-xs text-gray-500">Faltas</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ (int) ($freqResumo->faltas ?? 0) }}</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                    <div class="text-xs text-gray-500">Justificadas</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ (int) ($freqResumo->justificadas ?? 0) }}</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                    <div class="text-xs text-gray-500">Atrasos</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ (int) ($freqResumo->atrasos ?? 0) }}</div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Notas e médias (todas as disciplinas)</div>
                        <div class="text-xs text-gray-500">
                            @if(!empty($usarNotasBimestre))
                                Exibindo <strong>Notas do bimestre</strong> (quando lançadas) + ajuste manual (boletim).
                            @else
                                Média calculada por disciplina (ponderada pelo valor da avaliação) + ajuste manual (salva no boletim).
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-gray-50 px-4 py-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="text-sm text-gray-700">
                                <span class="font-medium">Período:</span> {{ $periodoSelecionado }}
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2">
                                <span class="text-xs font-semibold text-indigo-800 uppercase tracking-wide">
                                    {{ !empty($usarNotasBimestre) ? 'Média final (bimestre)' : 'Média das avaliações (calculada)' }}
                                </span>
                                <span class="text-2xl font-extrabold text-indigo-900 leading-none">
                                    @if(!empty($usarNotasBimestre))
                                        {{ $mediaFinalBimestre !== null ? number_format($mediaFinalBimestre, 2, ',', '.') : '—' }}
                                    @else
                                        {{ $mediaGeralCalculada !== null ? number_format($mediaGeralCalculada, 2, ',', '.') : '—' }}
                                    @endif
                                </span>
                            </div>
                            <div class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2">
                                <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Média manual (geral)</span>
                                <span class="text-2xl font-extrabold text-gray-900 leading-none">
                                    @php
                                        $mm = !empty($usarNotasBimestre) ? ($mediaGeralManualComNotasBimestre ?? null) : ($mediaGeralManual ?? null);
                                    @endphp
                                    {{ $mm !== null ? number_format($mm, 2, ',', '.') : '—' }}
                                </span>
                            </div>
                        </div>
                        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                            <label class="text-xs text-gray-500">Trocar período</label>
                            <select name="periodo" class="rounded-xl border-gray-300 text-sm" onchange="this.form.submit()">
                                @foreach(collect([$periodoSelecionado])->merge($periodos)->unique() as $per)
                                    <option value="{{ $per }}" @selected($per === $periodoSelecionado)>{{ $per }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($disciplinas as $did => $disc)
                        @php
                            $calc = $mediaPorDisciplina[$did] ?? null;
                            $bo = $boletins->get($did);
                            $notaBim = ($notasBimestrePorDisciplina[$did] ?? null);
                        @endphp
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $disc->nome }}</div>
                                    @if(!empty($usarNotasBimestre))
                                        <div class="text-xs text-gray-500">
                                            Nota do bimestre:
                                            <span class="font-medium text-gray-700">{{ $notaBim !== null ? number_format($notaBim, 2, ',', '.') : '—' }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            (Média por avaliações: {{ $calc !== null ? number_format($calc, 2, ',', '.') : '—' }})
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">Média calculada: <span class="font-medium text-gray-700">{{ $calc !== null ? number_format($calc, 2, ',', '.') : '—' }}</span></div>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('professor.alunos.media-manual.salvar', $matricula) }}" class="mt-3 flex flex-col sm:flex-row gap-2 sm:items-end">
                                @csrf
                                <input type="hidden" name="disciplina_id" value="{{ $did }}">
                                <input type="hidden" name="periodo" value="{{ $periodoSelecionado }}">
                                <div class="w-full sm:w-44">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Média manual</label>
                                    <input name="media" value="{{ $bo?->media }}" placeholder="Ex: 7,50" class="w-full rounded-xl border-gray-300 text-sm" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        onclick="const f=this.closest('form'); if(!f) return; const i=f.querySelector('input[name=media]'); if(i) i.value=''; f.submit();"
                                        class="px-3 py-2 rounded-xl bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm text-sm font-medium transition-colors">
                                        Limpar
                                    </button>
                                    <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Salvar</button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2 text-left">Disciplina</th>
                                <th class="px-4 py-2 text-left">Avaliação</th>
                                <th class="px-4 py-2 text-left">Data</th>
                                <th class="px-4 py-2 text-left">Valor</th>
                                <th class="px-4 py-2 text-left">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($avaliacoes as $av)
                                @php $n = $notasPorAvaliacao->get($av->id); @endphp
                                <tr>
                                    <td class="px-4 py-2 text-gray-700">{{ $av->disciplina?->nome ?? '—' }}</td>
                                    <td class="px-4 py-2 font-medium text-gray-900">{{ $av->titulo }}</td>
                                    <td class="px-4 py-2">{{ $av->data_avaliacao?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $av->valor ?? '—' }}</td>
                                    <td class="px-4 py-2">
                                        @if($n && $n->falta_na_avaliacao)
                                            <x-badge color="orange">Falta</x-badge>
                                        @else
                                            <span class="font-medium">{{ $n?->nota ?? '—' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Nenhuma avaliação cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="text-sm font-semibold text-gray-900 mb-2">Tarefas (registro por aluno)</div>
                @if($tarefas->isEmpty())
                    <div class="text-sm text-gray-600">Nenhuma tarefa cadastrada para esta turma.</div>
                @else
                    <div class="space-y-3">
                        @foreach($tarefas as $t)
                            @php $reg = $tarefasRegistro->get($t->id); @endphp
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $t->titulo }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $t->disciplina?->nome ?? '—' }} · Entrega: {{ $t->data_entrega?->format('d/m/Y') ?? '—' }}
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('professor.alunos.tarefas.salvar', $matricula) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="tarefa_id" value="{{ $t->id }}">
                                        <input type="hidden" name="periodo" value="{{ $periodoSelecionado }}">
                                        <select name="status" class="rounded-xl border-gray-300 text-sm">
                                            @php $st = $reg?->status ?? 'pendente'; @endphp
                                            <option value="pendente" @selected($st==='pendente')>Pendente</option>
                                            <option value="entregue" @selected($st==='entregue')>Entregue</option>
                                            <option value="nao_entregue" @selected($st==='nao_entregue')>Não entregou</option>
                                            <option value="fez" @selected($st==='fez')>Fez</option>
                                            <option value="nao_fez" @selected($st==='nao_fez')>Não fez</option>
                                        </select>
                                        <button class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Salvar</button>
                                    </form>
                                </div>
                                @if($reg?->observacao)
                                    <div class="mt-2 text-xs text-gray-600">Obs: {{ $reg->observacao }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="text-sm font-semibold text-gray-900 mb-2">Recados / comunicados para a turma</div>
                @if($avisosTurma->isEmpty())
                    <div class="text-sm text-gray-600">Nenhum comunicado recente para esta turma.</div>
                @else
                    <div class="space-y-3">
                        @foreach($avisosTurma as $a)
                            <div class="rounded-xl bg-gray-50 px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $a->titulo }}</div>
                                <div class="text-xs text-gray-500 mb-1">{{ $a->publicado_em ? $a->publicado_em->format('d/m/Y H:i') : '—' }}</div>
                                <div class="text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($a->mensagem, 220) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="text-sm font-semibold text-gray-900 mb-2">Últimas presenças/faltas</div>
                @if($ultimasFrequencias->isEmpty())
                    <div class="text-sm text-gray-600">Sem registros de frequência para este contexto.</div>
                @else
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-2 text-left">Data</th>
                                    <th class="px-4 py-2 text-left">Situação</th>
                                    <th class="px-4 py-2 text-left">Obs</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ultimasFrequencias as $f)
                                    <tr>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($f->data_aula)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">
                                            <x-badge color="{{ $f->situacao === 'presente' ? 'green' : ($f->situacao === 'falta' ? 'red' : 'gray') }}">
                                                {{ ucfirst($f->situacao) }}
                                            </x-badge>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">{{ $f->observacao ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-sigem-layout>

