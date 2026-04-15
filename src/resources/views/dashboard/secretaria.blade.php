@php
use App\Models\Institucional\{Escola, AnoLetivo};
use App\Models\Pessoas\{Aluno, Professor};
use App\Models\Academico\Matricula;

$totalEscolas  = Escola::count();
$totalAlunos   = Aluno::where('ativo', true)->count();
$totalProfs    = Professor::where('ativo', true)->count();
$anoAtivo      = AnoLetivo::where('ativo', true)->first();
$totalMatric   = $anoAtivo ? Matricula::where('ano_letivo_id', $anoAtivo->id)->where('situacao', 'ativa')->count() : 0;
@endphp

{{-- Cards de indicadores --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Escolas</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalEscolas }}</p>
        <p class="text-sm text-gray-500 mt-1">Unidades na rede</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Alunos</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalAlunos) }}</p>
        <p class="text-sm text-gray-500 mt-1">Alunos cadastrados</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-violet-600 bg-violet-50 px-2 py-1 rounded-full">Professores</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalProfs) }}</p>
        <p class="text-sm text-gray-500 mt-1">Professores ativos</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Matrículas</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalMatric) }}</p>
        <p class="text-sm text-gray-500 mt-1">
            {{ $anoAtivo ? 'Ativas em ' . $anoAtivo->descricao : 'Sem ano letivo ativo' }}
        </p>
    </div>

</div>

{{-- Linha inferior --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Ano letivo ativo --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Ano Letivo Atual
        </h3>
        @if($anoAtivo)
            <div class="bg-indigo-50 rounded-xl p-4">
                <p class="text-2xl font-bold text-indigo-700">{{ $anoAtivo->descricao }}</p>
                <p class="text-sm text-indigo-600 mt-1">
                    {{ $anoAtivo->data_inicio->format('d/m/Y') }} até {{ $anoAtivo->data_fim->format('d/m/Y') }}
                </p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-sm text-emerald-700 font-medium">Em andamento</span>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 rounded-xl p-4">
                <p class="text-yellow-700 text-sm">Nenhum ano letivo ativo. Configure um para começar.</p>
                <a href="#" class="mt-2 inline-flex items-center text-yellow-700 font-medium text-sm hover:underline">
                    Configurar ano letivo →
                </a>
            </div>
        @endif
    </div>

    {{-- Ações rápidas --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Acesso Rápido
        </h3>
        <div class="grid grid-cols-2 gap-3">
            @foreach([
                ['label' => 'Nova Escola', 'route' => '#', 'color' => 'blue'],
                ['label' => 'Novo Aluno', 'route' => '#', 'color' => 'emerald'],
                ['label' => 'Novo Professor', 'route' => '#', 'color' => 'violet'],
                ['label' => 'Nova Matrícula', 'route' => '#', 'color' => 'amber'],
            ] as $acao)
            <a href="{{ $acao['route'] }}"
               class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 border-dashed border-gray-200 text-sm font-medium text-gray-600 hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $acao['label'] }}
            </a>
            @endforeach
        </div>
    </div>

</div>
