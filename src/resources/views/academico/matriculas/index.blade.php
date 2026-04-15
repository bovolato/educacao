<x-sigem-layout title="Matrículas">
    <x-page-header title="Matrículas" subtitle="Gestão de matrículas dos alunos">
        <x-slot name="actions">
            <x-action-button href="{{ route('academico.matriculas.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Nova Matrícula
            </x-action-button>
        </x-slot>
    </x-page-header>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar aluno..."
            class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <select name="escola" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as escolas</option>
            @foreach($escolas as $e)
                <option value="{{ $e->id }}" @selected(request('escola') == $e->id)>{{ $e->nome }}</option>
            @endforeach
        </select>
        <select name="turma" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as turmas</option>
            @foreach($turmas as $t)
                <option value="{{ $t->id }}" @selected(request('turma') == $t->id)>{{ $t->nome }} ({{ $t->serie->nome ?? '' }})</option>
            @endforeach
        </select>
        <select name="situacao" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todas as situações</option>
            <option value="ativa" @selected(request('situacao') === 'ativa')>Ativa</option>
            <option value="transferida" @selected(request('situacao') === 'transferida')>Transferida</option>
            <option value="evadida" @selected(request('situacao') === 'evadida')>Evadida</option>
            <option value="concluida" @selected(request('situacao') === 'concluida')>Concluída</option>
            <option value="cancelada" @selected(request('situacao') === 'cancelada')>Cancelada</option>
        </select>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">Filtrar</button>
        @if(request()->hasAny(['busca', 'turma', 'situacao', 'escola']))
            <a href="{{ route('academico.matriculas.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">Limpar</a>
        @endif
    </form>

    <x-data-table>
        <x-slot name="head">
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Aluno</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Turma</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Nº Matrícula</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Data</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Situação</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Ações</th>
        </x-slot>

        @php
            $situacaoColors = ['ativa' => 'green', 'transferida' => 'yellow', 'evadida' => 'red', 'concluida' => 'blue', 'cancelada' => 'gray'];
        @endphp

        @forelse($matriculas as $m)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3.5 font-medium text-gray-800 text-sm">{{ $m->aluno->pessoa->nome }}</td>
                <td class="px-5 py-3.5">
                    <p class="text-sm text-gray-700">{{ $m->turma->nome ?? '—' }}</p>
                    <p class="text-xs text-gray-500">{{ $m->turma->serie->nome ?? '' }}</p>
                </td>
                <td class="px-5 py-3.5 text-sm text-gray-600 font-mono">{{ $m->numero_matricula ?? '—' }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-600">{{ $m->data_matricula?->format('d/m/Y') }}</td>
                <td class="px-5 py-3.5">
                    <x-badge :color="$situacaoColors[$m->situacao] ?? 'gray'" dot>{{ ucfirst($m->situacao) }}</x-badge>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('academico.matriculas.show', $m) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('academico.matriculas.edit', $m) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">Nenhuma matrícula encontrada.</td></tr>
        @endforelse

        <x-slot name="footer">
            <div class="flex items-center justify-between text-sm text-gray-500">
                <span>{{ $matriculas->total() }} matrícula(s)</span>
                {{ $matriculas->links() }}
            </div>
        </x-slot>
    </x-data-table>
</x-sigem-layout>
