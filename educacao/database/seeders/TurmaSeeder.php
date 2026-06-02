<?php

namespace Database\Seeders;

use App\Models\Institucional\{Escola, AnoLetivo, Serie, Turno, Sala};
use App\Models\Academico\{Turma, Disciplina};
use App\Models\Pessoas\Professor;
use Illuminate\Database\Seeder;

class TurmaSeeder extends Seeder
{
    public function run(): void
    {
        $escola    = Escola::first();
        $anoAtivo  = AnoLetivo::where('ativo', true)->first();
        $series    = Serie::where('ativo', true)->get();
        $turnos    = Turno::where('ativo', true)->get();
        $salas     = Sala::where('escola_id', $escola->id)->get();
        $disciplinas = Disciplina::where('ativo', true)->get();
        $professores = Professor::where('ativo', true)->get();

        if ($series->isEmpty() || $turnos->isEmpty()) {
            $this->command->warn('Séries ou turnos não encontrados. Execute MunicipioSeeder primeiro.');
            return;
        }

        $turnosManha = $turnos->where('nome', 'Manhã')->first() ?? $turnos->first();
        $turnosTarde = $turnos->where('nome', 'Tarde')->first() ?? $turnos->first();

        $letras = ['A', 'B', 'C'];

        $turmaIndex = 0;
        foreach ($series->take(8) as $serieIndex => $serie) {
            foreach (array_slice($letras, 0, ($serieIndex % 2 === 0 ? 2 : 1)) as $letra) {
                $turno = $serieIndex % 2 === 0 ? $turnosManha : $turnosTarde;
                $sala  = $salas->isNotEmpty() ? $salas[$turmaIndex % $salas->count()] : null;

                $turma = Turma::create([
                    'escola_id'    => $escola->id,
                    'ano_letivo_id' => $anoAtivo->id,
                    'serie_id'     => $serie->id,
                    'turno_id'     => $turno->id,
                    'sala_id'      => $sala?->id,
                    'nome'         => $serie->sigla . ' ' . $letra,
                    'codigo'       => strtoupper($serie->sigla) . $letra,
                    'capacidade'   => 35,
                    'status'       => 'ativa',
                ]);

                // Vincular disciplinas à turma
                $disciplinasParaTurma = $disciplinas->take(min(8, $disciplinas->count()));
                foreach ($disciplinasParaTurma as $disc) {
                    $turma->disciplinas()->syncWithoutDetaching([
                        $disc->id => ['carga_horaria' => $disc->carga_horaria ?? 80],
                    ]);
                }

                // Vincular professores à turma (1 professor por disciplina)
                foreach ($disciplinasParaTurma as $dIdx => $disc) {
                    $professor = $professores->isNotEmpty()
                        ? $professores[$dIdx % $professores->count()]
                        : null;

                    if ($professor) {
                        // Evitar duplicata (mesma turma + disciplina + professor)
                        \DB::table('turma_professores')
                            ->updateOrInsert(
                                ['turma_id' => $turma->id, 'disciplina_id' => $disc->id, 'professor_id' => $professor->id],
                                ['titular' => true, 'created_at' => now(), 'updated_at' => now()]
                            );
                    }
                }

                $turmaIndex++;
            }
        }
    }
}
