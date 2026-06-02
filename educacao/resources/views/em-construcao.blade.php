<x-sigem-layout :title="$titulo ?? 'Em Construção'">

    <div class="flex flex-col items-center justify-center min-h-96 text-center">
        <div class="w-20 h-20 rounded-2xl bg-amber-100 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $titulo ?? 'Em Construção' }}</h2>
        <p class="text-gray-500 max-w-md">
            Este módulo está sendo desenvolvido e estará disponível em breve.
        </p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar ao Dashboard
        </a>
    </div>

</x-sigem-layout>
