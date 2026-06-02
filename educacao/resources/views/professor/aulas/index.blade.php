<x-sigem-layout title="Aulas — conteúdo">
    <x-page-header title="Aulas e conteúdo ministrado" subtitle="Registre aulas e descreva o conteúdo ministrado em cada data"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas">
        <x-slot name="actions">
            @php
                $poli = (bool) ($turmaSelecionada?->polivalente ?? false);
                $temContexto = request()->filled('turma_id') && ($poli || request()->filled('disciplina_id'));
            @endphp

            @if($temContexto)
                <x-action-button href="{{ $poli
                    ? route('professor.aulas.create', ['turma_id' => request('turma_id')])
                    : route('professor.aulas.create', ['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')]) }}">Nova aula</x-action-button>
            @endif
        </x-slot>
    </x-page-header>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if($vinculos->isEmpty())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Você ainda não tem turmas/disciplinas vinculadas. Peça ao gestor da escola para concluir o vínculo.
        </div>
    @endif

    <x-professor-modulo-shell
        :vinculos="$vinculos"
        :turma-id="request('turma_id')"
        :disciplina-id="request('disciplina_id')"
        active="aulas"
        module-route="professor.aulas.index"
    >
        @php
            $poli = (bool) ($turmaSelecionada?->polivalente ?? false);
            $contextoOk = request()->filled('turma_id') && ($poli || request()->filled('disciplina_id'));
        @endphp

        @if($contextoOk && $aulas && $aulas->total() > 0)
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3">Data</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Registro de conteúdo</th>
                            <th class="px-5 py-3">Frequência</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $totalMatriculas = $turmaSelecionada?->matriculasAtivas()?->count() ?? null;
                        @endphp
                        @foreach($aulas as $aula)
                            @php
                                $lancadas = (int) ($aula->frequencias_count ?? 0);
                                $concluida = $totalMatriculas !== null && $totalMatriculas > 0 && $lancadas >= $totalMatriculas;
                            @endphp
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $aula->data_aula?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3"><span class="capitalize">{{ str_replace('_', ' ', $aula->status) }}</span></td>
                                <td class="px-5 py-3">
                                    @if($aula->conteudos_count > 0)
                                        <span class="text-emerald-700 font-medium">Preenchido</span>
                                    @else
                                        <span class="text-amber-700">Pendente</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($totalMatriculas !== null)
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-badge color="{{ $concluida ? 'green' : ($lancadas > 0 ? 'yellow' : 'gray') }}">
                                                {{ $concluida ? 'Concluída' : ($lancadas > 0 ? 'Em andamento' : 'Pendente') }}
                                            </x-badge>
                                            <span class="text-xs text-gray-500">{{ $lancadas }}/{{ $totalMatriculas }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-4">
                                        <a href="{{ route('professor.frequencias.aula.edit', $aula) }}" class="text-indigo-600 hover:underline font-medium">Lançar presença</a>
                                        <a href="{{ route('professor.aulas.conteudo', $aula) }}" class="text-indigo-600 hover:underline font-medium">Registrar / editar conteúdo</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">{{ $aulas->links() }}</div>
            </div>
        @elseif($contextoOk && $aulas && $aulas->total() === 0)
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-gray-700 font-medium mb-1">Nenhuma aula registrada ainda</p>
                <p class="text-sm text-gray-500 mb-5">Cadastre aulas (datas) para esta turma{{ $poli ? '' : ' e disciplina' }}. Depois você poderá registrar o conteúdo e lançar frequência.</p>
                <a href="{{ $poli
                        ? route('professor.aulas.create', ['turma_id' => request('turma_id')])
                        : route('professor.aulas.create', ['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')]) }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                    Registrar primeira aula
                </a>
            </div>
        @elseif(! $contextoOk)
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm text-gray-600">
                Selecione <strong>turma</strong> (e disciplina quando necessário) para ver e cadastrar aulas.
            </div>
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>
