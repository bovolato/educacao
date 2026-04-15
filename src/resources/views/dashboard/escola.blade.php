@php
use App\Models\Academico\{Turma, Matricula};
use App\Models\Pessoas\{Aluno, Professor};

$escola     = Auth::user()->escola;
$anoAtivo   = \App\Models\Institucional\AnoLetivo::where('ativo', true)->first();
$totalTurmas = $escola ? Turma::where('escola_id', $escola->id)->where('status', 'ativa')->count() : 0;
$totalAlunos = $escola && $anoAtivo ? Matricula::where('escola_id', $escola->id)->where('ano_letivo_id', $anoAtivo->id)->where('situacao', 'ativa')->count() : 0;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalTurmas }}</p>
        <p class="text-sm text-gray-500 mt-1">Turmas ativas</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalAlunos }}</p>
        <p class="text-sm text-gray-500 mt-1">Alunos matriculados</p>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-200">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-xl font-bold text-gray-800">{{ $anoAtivo?->descricao ?? '—' }}</p>
        <p class="text-sm text-gray-500 mt-1">Ano letivo ativo</p>
    </div>
</div>

@if($escola)
<div class="bg-white rounded-2xl p-5 border border-gray-200">
    <h3 class="font-semibold text-gray-800 mb-2">{{ $escola->nome }}</h3>
    <p class="text-sm text-gray-500">{{ $escola->logradouro }}, {{ $escola->numero }} — {{ $escola->bairro }}</p>
</div>
@endif
