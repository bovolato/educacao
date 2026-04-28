<x-sigem-layout title="Notas (bimestre)">
    <x-page-header title="Notas (bimestre)" subtitle="Uma lista por bimestre com notas por disciplina e média final do aluno"
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
        active="notas_bimestre"
        module-route="professor.notas-bimestre.index"
    >
        @php
            $contextoOk = request()->filled('turma_id');
        @endphp

        @if(! $contextoOk)
            <x-empty-state
                title="Selecione o contexto"
                subtitle="Escolha a turma acima para criar/editar a lista de notas do bimestre."
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
                            <x-action-button href="{{ route('professor.notas-bimestre.edit', $lista) }}">
                                Abrir / editar lista
                            </x-action-button>
                        @else
                            <x-action-button href="{{ route('professor.notas-bimestre.create', ['turma_id' => request('turma_id')]) }}">
                                Criar lista do bimestre
                            </x-action-button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-professor-modulo-shell>
</x-sigem-layout>

