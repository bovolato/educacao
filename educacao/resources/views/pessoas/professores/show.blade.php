<x-sigem-layout :title="$professor->nome">
    <x-page-header :title="$professor->nome" subtitle="Ficha do Professor" :back-route="route('pessoas.professores.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('pessoas.professores.vincular-turmas', $professor) }}" variant="secondary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>'>
                Vincular Turmas
            </x-action-button>
            @if(isset($usuarioVinculado) && $usuarioVinculado?->password)
                <x-action-button href="{{ route('pessoas.professores.usuario.form', $professor) }}" variant="secondary">
                    Ver login
                </x-action-button>
            @else
                <x-action-button href="{{ route('pessoas.professores.usuario.form', $professor) }}" variant="secondary">
                    Criar login
                </x-action-button>
            @endif
            <x-action-button href="{{ route('pessoas.professores.edit', $professor) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>

    @if(! $professor->usuario)
        <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <strong>Cadastro incompleto:</strong> não há registro de usuario vinculado a este professor. Os dados pessoais e contatos não podem ser exibidos. Corrija o vínculo (<code class="text-xs">pessoa_id</code>) no banco de dados.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados Funcionais</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 uppercase mb-0.5">Escola de Atuação</p>
                        @if($professor->escola)
                            <a href="{{ route('admin.escolas.show', $professor->escola) }}" class="font-medium text-indigo-600 hover:underline">{{ $professor->escola->nome }}</a>
                        @else
                            <p class="font-medium text-gray-800">—</p>
                        @endif
                    </div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Município (vínculo)</p><p class="font-medium">{{ $professor->municipio?->nome ?? ($professor->escola?->cidade ?? '—') }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Matrícula</p><p class="font-mono font-medium">{{ $professor->matricula_funcional ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Registro Prof.</p><p class="font-medium">{{ $professor->registro_profissional ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Formação</p><p class="font-medium">{{ $professor->formacao ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Admissão</p><p class="font-medium">{{ $professor->data_admissao?->format('d/m/Y') ?? '—' }}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase mb-0.5">Status</p>
                        <x-badge :color="$professor->ativo ? 'green' : 'gray'" dot>{{ $professor->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800">Turmas e disciplinas</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Uma linha por turma; disciplinas lecionadas aparecem agrupadas (não é turma repetida).</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="text-sm text-gray-500">{{ $professor->turmas->unique('id')->count() }} turma(s)</span>
                        <a href="{{ route('pessoas.professores.vincular-turmas', $professor) }}" class="text-xs text-indigo-600 hover:underline font-medium">Gerenciar</a>
                    </div>
                </div>
                @if($professor->turmas->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">Nenhuma turma vinculada. <a href="{{ route('pessoas.professores.vincular-turmas', $professor) }}" class="text-indigo-600 hover:underline">Vincular agora</a></p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($professor->turmas->groupBy('id') as $turmaGrupo)
                            @php $turma = $turmaGrupo->first(); @endphp
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $turma->nome }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $turma->serie->nome ?? '—' }} · {{ $turma->turno->nome ?? '—' }}</p>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach($turmaGrupo as $entrada)
                                            @php
                                                $disciplina = \App\Models\Academico\Disciplina::find($entrada->pivot->disciplina_id);
                                            @endphp
                                            <span class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 text-indigo-800 px-2 py-0.5 text-xs font-medium">
                                                {{ $disciplina?->nome ?? '—' }}
                                                @if($entrada->pivot->titular)
                                                    <x-badge color="blue">Titular</x-badge>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <a href="{{ route('academico.turmas.show', $turma) }}" class="text-xs text-indigo-600 hover:underline shrink-0 self-start sm:self-center">Ver turma</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Dados Pessoais</h3>
                @if($professor->usuario)
                    <div class="space-y-3">
                        @php $pp = $professor->usuario; @endphp
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">CPF</p><p class="text-sm font-medium">{{ $pp->cpf ?? '—' }}</p></div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Nascimento</p>
                            <p class="text-sm font-medium">{{ $pp->data_nascimento ? \Carbon\Carbon::parse($pp->data_nascimento)->format('d/m/Y') : '—' }}</p>
                        </div>
                        <div><p class="text-xs text-gray-500 uppercase mb-0.5">Sexo</p>
                            <p class="text-sm font-medium">{{ $pp->sexo === 'M' ? 'Masculino' : ($pp->sexo === 'F' ? 'Feminino' : '—') }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">—</p>
                @endif
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Contatos</h3>
                <div class="space-y-2">
                    @if($professor->usuario)
                        @forelse($professor->usuario->contatos as $contato)
                            <div>
                                <p class="text-xs text-gray-500 uppercase">{{ $contato->tipo }}</p>
                                <p class="text-sm font-medium text-gray-700">{{ $contato->valor }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Nenhum contato cadastrado.</p>
                        @endforelse
                    @else
                        <p class="text-sm text-gray-500">—</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-sigem-layout>
