<x-sigem-layout title="Turmas">
    <x-page-header title="Turmas" subtitle="Gestão das turmas do ano letivo">
        <x-slot name="actions">
            <x-action-button href="{{ route('academico.turmas.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Nova Turma
            </x-action-button>
        </x-slot>
    </x-page-header>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar turma..."
            class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <select name="cidade" class="min-w-[160px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as cidades</option>
            @foreach($cidades as $cidade)
                <option value="{{ $cidade }}" @selected(request('cidade') === $cidade)>{{ $cidade }}</option>
            @endforeach
        </select>
        <select name="escola" class="min-w-[200px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as escolas</option>
            @foreach($escolas as $e)
                <option value="{{ $e->id }}" @selected((string) request('escola') === (string) $e->id)>{{ $e->nome }}</option>
            @endforeach
        </select>
        <select name="serie" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as séries</option>
            @foreach($series as $s)
                <option value="{{ $s->id }}" @selected(request('serie') == $s->id)>{{ $s->nome }}</option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todos os status</option>
            <option value="ativa" @selected(request('status') === 'ativa')>Ativa</option>
            <option value="encerrada" @selected(request('status') === 'encerrada')>Encerrada</option>
            <option value="suspensa" @selected(request('status') === 'suspensa')>Suspensa</option>
        </select>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">Filtrar</button>
        @if(request()->hasAny(['busca', 'cidade', 'escola', 'serie', 'status']))
            <a href="{{ route('academico.turmas.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">Limpar</a>
        @endif
    </form>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Turma</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Série / Turno</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Escola / Cidade</th>
            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Alunos</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @forelse($turmas as $turma)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3.5 font-medium text-gray-800">{{ $turma->nome }}</td>
                <td class="px-5 py-3.5">
                    <p class="text-sm text-gray-700">{{ $turma->serie->nome ?? '—' }}</p>
                    <p class="text-xs text-gray-500">{{ $turma->turno->nome ?? '—' }}</p>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-600">
                    <p class="font-medium text-gray-800">{{ $turma->escola->nome ?? '—' }}</p>
                    @if($turma->escola?->cidade)
                        <p class="text-xs text-gray-500">{{ $turma->escola->cidade }}{{ $turma->escola->uf ? '/' . $turma->escola->uf : '' }}</p>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-sm font-semibold">
                        {{ $turma->matriculas_ativas_count }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    @php $statusColors = ['ativa' => 'green', 'encerrada' => 'gray', 'suspensa' => 'yellow']; @endphp
                    <x-badge :color="$statusColors[$turma->status] ?? 'gray'" dot>{{ ucfirst($turma->status) }}</x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('academico.turmas.show', $turma) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('academico.turmas.edit', $turma) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">Nenhuma turma encontrada.</td></tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $turmas->total() }} turma(s)</span>
                {{ $turmas->links() }}
            </div>
        </x-slot>
    </x-data-table>
</x-sigem-layout>
