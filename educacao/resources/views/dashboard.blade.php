<x-sigem-layout title="Dashboard">

    {{-- Boas-vindas --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Olá, {{ Auth::user()->nome }}!
        </h2>
        <p class="text-gray-500 mt-1">
            Bem-vindo ao SIGEM — Sistema Integrado de Gestão Educacional Municipal
        </p>
    </div>

    @php $user = Auth::user(); @endphp

    {{-- DASHBOARD SECRETARIA / SUPER ADMIN --}}
    @if($user->isSecretariaMunicipal())
        @include('dashboard.secretaria')

    {{-- DASHBOARD GESTOR ESCOLAR --}}
    @elseif($user->isGestorEscolar() || $user->hasRole('secretario_escolar'))
        @include('dashboard.escola')

    {{-- DASHBOARD PROFESSOR --}}
    @elseif($user->isProfessor())
        @include('dashboard.professor')

    {{-- DASHBOARD ALUNO / RESPONSÁVEL --}}
    @elseif($user->isAluno() || $user->isResponsavel())
        @include('dashboard.portal')

    {{-- DEFAULT --}}
    @else
        <div class="bg-white rounded-2xl p-8 text-center border border-gray-200">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Sistema SIGEM</h3>
            <p class="text-gray-500">Seu perfil ainda não possui um dashboard configurado. Entre em contato com o administrador.</p>
        </div>
    @endif

</x-sigem-layout>
