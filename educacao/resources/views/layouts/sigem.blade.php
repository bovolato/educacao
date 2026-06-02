<!DOCTYPE html>
<html lang="pt-BR" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'SIGEM') }}</title>

    {{-- Fonte sem bloquear renderização (LCP): carrega como "print" e aplica ao terminar --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet"
          href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap"
          media="print"
          onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap">
    </noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    @php($userNav = auth()->user())

    <div class="flex h-screen overflow-hidden">

        {{-- Overlay mobile --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"
        ></div>

        {{-- SIDEBAR --}}
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-30 flex flex-col bg-indigo-900 transition-all duration-300 ease-in-out lg:static lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarCollapsed ? 'w-16' : 'w-64'
            ]"
        >
            {{-- Logo --}}
            <div class="flex items-center justify-between h-16 px-4 bg-indigo-950 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-400 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-white font-bold text-lg tracking-tight truncate">SIGEM</span>
                </a>
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-indigo-300 hover:text-white p-1 rounded">
                    <svg x-show="!sidebarCollapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    <svg x-show="sidebarCollapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Perfil do usuário --}}
            <div x-show="!sidebarCollapsed" class="px-4 py-3 border-b border-indigo-800">
                <p class="text-xs text-indigo-300 uppercase tracking-widest mb-1">Logado como</p>
                <p class="text-white text-sm font-medium truncate">{{ $userNav->nome }}</p>
                <p class="text-indigo-300 text-xs truncate">{{ $userNav->roles->first()?->name ?? 'Sem perfil' }}</p>
            </div>

            {{-- Navegação --}}
            <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">

                @include('layouts.partials.sidebar-menu')

            </nav>

            {{-- Rodapé sidebar --}}
            <div class="shrink-0 border-t border-indigo-800 p-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-indigo-300 hover:bg-indigo-800 hover:text-white transition-colors group">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="text-sm">Sair do sistema</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- CONTEÚDO PRINCIPAL --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Topbar --}}
            <header class="flex items-center gap-4 h-16 px-4 lg:px-6 bg-white border-b border-gray-200 shrink-0">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1">
                    @isset($title)
                        <h1 class="text-lg font-semibold text-gray-800">{{ $title }}</h1>
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    {{-- Notificações --}}
                    <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    {{-- Avatar --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ strtoupper(substr($userNav->nome, 0, 2)) }}</span>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700">{{ $userNav->nome }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-1 z-10">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Meu Perfil
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Mensagens flash --}}
            @if(session('success') || session('error') || session('warning'))
            <div class="px-4 lg:px-6 pt-4">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800">
                        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">✕</button>
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show"
                        class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">{{ session('error') }}</p>
                        <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">✕</button>
                    </div>
                @endif
                @if(session('warning'))
                    <div x-data="{ show: true }" x-show="show"
                        class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800">
                        <svg class="w-5 h-5 text-yellow-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm">{{ session('warning') }}</p>
                        <button @click="show = false" class="ml-auto text-yellow-600 hover:text-yellow-800">✕</button>
                    </div>
                @endif
            </div>
            @endif

            {{-- Conteúdo da página --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>
