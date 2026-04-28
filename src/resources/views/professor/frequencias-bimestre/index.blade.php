<x-sigem-layout title="Frequência (bimestre)">
    <x-page-header title="Frequência (bimestre)" subtitle="Uma lista por bimestre com contagem de presenças/faltas por aluno"
        :back-route="route('professor.turmas.index')" back-label="Minhas turmas"/>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
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

        @if(! $contextoOk)
            <x-empty-state
                title="Selecione o contexto"
                subtitle="Escolha a turma (e disciplina quando necessário) para criar/editar a lista de presença do bimestre."
            />
        @else
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Bimestre atual</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $periodoAtual }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            @if($lista)
                                Lista criada de <strong>{{ $lista->data_inicio?->format('d/m/Y') }}</strong> até <strong>{{ $lista->data_fim?->format('d/m/Y') }}</strong>.
                            @else
                                Nenhuma lista criada ainda para este bimestre.
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($lista)
                            <a href="{{ route('professor.frequencias.edit', $lista) }}"
                                class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Abrir / editar lista
                            </a>
                        @else
                            <a href="{{ route('professor.frequencias.create', array_filter(['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')])) }}"
                                class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Criar lista do bimestre
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <strong>Nota:</strong> a frequência por aula continua disponível no modo legado, mas o fluxo recomendado agora é por bimestre.
                    </div>
                    <a href="{{ route('professor.frequencias.legado.index', array_filter(['turma_id' => request('turma_id'), 'disciplina_id' => request('disciplina_id')])) }}"
                        class="text-sm text-amber-900 underline hover:no-underline font-medium">
                        Abrir frequência por aula (legado)
                    </a>
                </div>
            </div>
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>

