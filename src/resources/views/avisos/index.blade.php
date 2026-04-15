<x-sigem-layout title="Avisos">
    <x-page-header title="Avisos e Comunicados" subtitle="Comunicados para a comunidade escolar">
        <x-slot name="actions">
            <x-action-button href="{{ route('avisos.create') }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>'>
                Novo Aviso
            </x-action-button>
        </x-slot>
    </x-page-header>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar aviso..."
            class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        <select name="tipo" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">Todos os tipos</option>
            <option value="geral" @selected(request('tipo') === 'geral')>Geral</option>
            <option value="escola" @selected(request('tipo') === 'escola')>Escola</option>
            <option value="turma" @selected(request('tipo') === 'turma')>Turma</option>
        </select>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">Filtrar</button>
        @if(request()->hasAny(['busca', 'tipo']))
            <a href="{{ route('avisos.index') }}" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">Limpar</a>
        @endif
    </form>

    <div class="space-y-3">
        @forelse($avisos as $aviso)
            <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-sm transition-shadow">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            @php
                                $tipoColors = ['geral' => 'blue', 'escola' => 'green', 'turma' => 'purple'];
                            @endphp
                            <x-badge :color="$tipoColors[$aviso->tipo_destino] ?? 'gray'">{{ ucfirst($aviso->tipo_destino) }}</x-badge>
                            @if(!$aviso->ativo) <x-badge color="gray">Inativo</x-badge> @endif
                        </div>
                        <h3 class="font-semibold text-gray-800 text-base">{{ $aviso->titulo }}</h3>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $aviso->mensagem }}</p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            <span>{{ $aviso->usuario->name ?? '—' }}</span>
                            <span>·</span>
                            <span>{{ $aviso->publicado_em?->format('d/m/Y H:i') }}</span>
                            @if($aviso->escola)
                                <span>·</span>
                                <span>{{ $aviso->escola->nome }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('avisos.show', $aviso) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        <a href="{{ route('avisos.edit', $aviso) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                        <form method="POST" action="{{ route('avisos.destroy', $aviso) }}" onsubmit="return confirm('Desativar este aviso?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-200 py-16 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-500">Nenhum aviso encontrado.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-5">{{ $avisos->links() }}</div>
</x-sigem-layout>
