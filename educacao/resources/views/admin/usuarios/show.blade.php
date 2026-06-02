<x-sigem-layout :title="$usuario->nome">
    <x-page-header :title="$usuario->nome" :back-route="route('admin.usuarios.index')" back-label="Voltar">
        <x-slot name="actions">
            <x-action-button href="{{ route('admin.usuarios.edit', $usuario) }}" variant="secondary">Editar</x-action-button>
        </x-slot>
    </x-page-header>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Dados do Usuário</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">E-mail</p><p class="font-medium">{{ $usuario->email }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">Username</p><p class="font-medium">{{ $usuario->username ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">Município</p><p class="font-medium">{{ $usuario->municipio->nome ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">Escola</p><p class="font-medium">{{ $usuario->escola->nome ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">Último Acesso</p><p class="font-medium">{{ $usuario->ultimo_login_em?->format('d/m/Y H:i') ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500 uppercase mb-0.5">Status</p>
                    <x-badge :color="$usuario->ativo ? 'green' : 'gray'" dot>{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</x-badge>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Perfis de Acesso</h3>
            <div class="flex flex-wrap gap-2">
                @forelse($usuario->roles as $role)
                    <x-badge color="indigo">{{ $role->name }}</x-badge>
                @empty
                    <p class="text-sm text-gray-500">Sem perfil atribuído.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-sigem-layout>
