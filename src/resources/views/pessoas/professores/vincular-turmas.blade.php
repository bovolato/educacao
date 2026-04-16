<x-sigem-layout :title="'Vincular Turmas — ' . $professor->nome">

    <x-page-header :title="'Vincular Turmas'" :subtitle="$professor->nome . ' — ' . ($professor->escola?->nome ?? '')"
        :back-route="route('pessoas.professores.show', $professor)" back-label="Voltar para Ficha" />

    <form method="POST" action="{{ route('pessoas.professores.salvar-vinculo-turmas', $professor) }}"
          x-data="vinculoTurmas()" x-cloak>
        @csrf

        <x-form-card title="Vínculos Turma / Disciplina">
            <p class="text-sm text-gray-500 mb-4">Adicione as turmas e disciplinas em que este professor leciona na escola <strong>{{ $professor->escola?->nome }}</strong>.</p>

            <div class="space-y-3" id="vinculos-container">
                <template x-for="(vinculo, index) in vinculos" :key="index">
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex-1 min-w-0">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Turma</label>
                            <select :name="'vinculos['+index+'][turma_id]'" x-model="vinculo.turma_id"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">Selecione a turma...</option>
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->nome }} — {{ $turma->serie->nome ?? '' }} / {{ $turma->turno->nome ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Disciplina</label>
                            <select :name="'vinculos['+index+'][disciplina_id]'" x-model="vinculo.disciplina_id"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="">Selecione a disciplina...</option>
                                @foreach($disciplinas as $disc)
                                    <option value="{{ $disc->id }}">{{ $disc->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-3 pt-1">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" :name="'vinculos['+index+'][titular]'" value="1" x-model="vinculo.titular"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-600">Titular</span>
                            </label>
                            <button type="button" @click="removeVinculo(index)"
                                class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Remover">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-4">
                <button type="button" @click="addVinculo()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar Vínculo
                </button>
            </div>
        </x-form-card>

        <div class="flex justify-end gap-3">
            <x-action-button href="{{ route('pessoas.professores.show', $professor) }}" variant="secondary">Cancelar</x-action-button>
            <x-action-button type="submit" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'>
                Salvar Vínculos
            </x-action-button>
        </div>
    </form>

    <script>
        function vinculoTurmas() {
            const existentes = @json(
                DB::table('turma_professores')
                    ->where('professor_id', $professor->id)
                    ->get(['turma_id', 'disciplina_id', 'titular'])
            );

            return {
                vinculos: existentes.length > 0
                    ? existentes.map(v => ({ turma_id: String(v.turma_id), disciplina_id: String(v.disciplina_id), titular: Boolean(v.titular) }))
                    : [{ turma_id: '', disciplina_id: '', titular: true }],

                addVinculo() {
                    this.vinculos.push({ turma_id: '', disciplina_id: '', titular: true });
                },

                removeVinculo(index) {
                    if (this.vinculos.length > 0) {
                        this.vinculos.splice(index, 1);
                    }
                }
            };
        }
    </script>

</x-sigem-layout>
