<x-sigem-layout title="Frequência">
    <x-page-header title="Frequência" subtitle="Registre presença por aula (turma e disciplina)"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas"/>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if($vinculos->isEmpty())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Você ainda não tem turmas/disciplinas vinculadas. Peça ao gestor da escola para concluir o vínculo em <strong>Professores → Vincular turmas</strong>.
        </div>
    @endif

    <x-professor-modulo-shell
        :vinculos="$vinculos"
        :turma-id="request('turma_id')"
        :disciplina-id="request('disciplina_id')"
        active="frequencias"
        module-route="professor.frequencias.index"
    >
        @php
            $contextoOk = request()->filled('turma_id') && (
                ($turmaSelecionada?->polivalente ?? false) || request()->filled('disciplina_id')
            );
        @endphp

        @if($contextoOk && $aulas && $aulas->total() > 0)
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-5 py-3">Data da aula</th>
                            <th class="px-5 py-3">Status</th>
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
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="capitalize text-gray-700">{{ str_replace('_', ' ', $aula->status) }}</span>
                                        @if($totalMatriculas !== null)
                                            <x-badge color="{{ $concluida ? 'green' : ($lancadas > 0 ? 'yellow' : 'gray') }}">
                                                {{ $concluida ? 'Concluída' : ($lancadas > 0 ? 'Em andamento' : 'Pendente') }}
                                            </x-badge>
                                            <span class="text-xs text-gray-500">{{ $lancadas }}/{{ $totalMatriculas }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('professor.frequencias.aula.edit', $aula) }}" class="text-indigo-600 hover:underline font-medium">Lançar / editar frequência</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">{{ $aulas->links() }}</div>
            </div>
        @elseif($contextoOk && $aulas && $aulas->total() === 0)
            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-gray-700 font-medium mb-1">Nenhuma aula cadastrada</p>
                <p class="text-sm text-gray-500 mb-5">Para lançar frequência, primeiro registre pelo menos uma aula com a data desejada.</p>
                <x-action-button href="{{ ($turmaSelecionada?->polivalente ?? false)
                        ? route('professor.aulas.create', ['turma_id' => request('turma_id')])
                        : route('professor.aulas.create', ['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')]) }}">
                    Registrar nova aula
                </x-action-button>
            </div>
        @elseif(! $contextoOk)
            <x-empty-state
                title="Selecione o contexto"
                subtitle="Escolha a turma (e disciplina quando necessário) acima para ver as aulas e lançar a frequência."
            />
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>
