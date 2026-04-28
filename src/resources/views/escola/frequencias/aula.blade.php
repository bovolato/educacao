<x-sigem-layout title="Diário de frequência">
    <x-page-header title="Frequência da aula" :subtitle="$aula->turma->nome . ' — ' . ($aula->disciplina?->nome ?? '')"
        :back-route="route('escola.frequencias.turma', $aula->turma)" back-label="Voltar"/>

    <div class="mb-4 text-sm text-gray-600">
        <strong>Data:</strong> {{ $aula->data_aula?->format('d/m/Y') }}
        · <strong>Professor:</strong> {{ $aula->professor?->pessoa?->nome ?? '—' }}
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3 font-medium">Aluno</th>
                    <th class="px-5 py-3 font-medium">Situação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($matriculas as $mat)
                    @php $f = $freqPorMatricula->get($mat->id); @endphp
                    <tr>
                        <td class="px-5 py-3 text-gray-900">{{ $mat->aluno?->pessoa?->nome ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($f)
                                @php
                                    $cor = $f->situacao === 'presente' ? 'green' : ($f->situacao === 'falta' ? 'red' : ($f->situacao === 'justificada' ? 'blue' : 'gray'));
                                @endphp
                                <x-badge :color="$cor" dot>{{ ucfirst(str_replace('_', ' ', $f->situacao)) }}</x-badge>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-sigem-layout>
