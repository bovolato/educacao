<?php

namespace Database\Seeders;

use App\Models\Academico\{Turma, Avaliacao, Nota, Matricula};
use Illuminate\Database\Seeder;

class AvaliacaoNotaSeeder extends Seeder
{
    public function run(): void
    {
        $turmas = Turma::where('status', 'ativa')
            ->with(['disciplinas', 'matriculasAtivas'])
            ->get();

        foreach ($turmas as $turma) {
            $disciplinas = $turma->disciplinas;
            if ($disciplinas->isEmpty()) {
                continue;
            }

            foreach ($disciplinas->take(4) as $disciplina) {
                $profPivot = \DB::table('turma_professores')
                    ->where('turma_id', $turma->id)
                    ->where('disciplina_id', $disciplina->id)
                    ->first();

                if (!$profPivot) {
                    continue;
                }

                // 3 avaliações por disciplina (1º, 2º e 3º bimestre)
                $avaliacoes = [
                    ['titulo' => 'Avaliação 1º Bimestre', 'periodo' => '1B', 'data' => now()->subDays(60)->toDateString()],
                    ['titulo' => 'Avaliação 2º Bimestre', 'periodo' => '2B', 'data' => now()->subDays(30)->toDateString()],
                    ['titulo' => 'Avaliação 3º Bimestre', 'periodo' => '3B', 'data' => now()->subDays(7)->toDateString()],
                ];

                foreach ($avaliacoes as $avDados) {
                    $avaliacao = Avaliacao::create([
                        'turma_id'       => $turma->id,
                        'disciplina_id'  => $disciplina->id,
                        'professor_id'   => $profPivot->professor_id,
                        'titulo'         => $avDados['titulo'],
                        'tipo'           => 'prova',
                        'data_avaliacao' => $avDados['data'],
                        'valor'          => 10.00,
                        'periodo'        => $avDados['periodo'],
                    ]);

                    // Lançar notas para cada aluno
                    $matriculas = $turma->matriculasAtivas()->get();
                    foreach ($matriculas as $matricula) {
                        // Distribuição realista de notas (curva normal simplificada)
                        $rand = rand(1, 100);
                        if ($rand <= 5) {
                            $nota = round(rand(0, 20) / 10, 1);      // 5% nota baixa
                        } elseif ($rand <= 20) {
                            $nota = round(rand(30, 49) / 10, 1);     // 15% nota média-baixa
                        } elseif ($rand <= 60) {
                            $nota = round(rand(50, 79) / 10, 1);     // 40% nota média
                        } else {
                            $nota = round(rand(80, 100) / 10, 1);    // 40% nota boa
                        }

                        Nota::create([
                            'avaliacao_id'      => $avaliacao->id,
                            'aluno_id'          => $matricula->aluno_id,
                            'matricula_id'      => $matricula->id,
                            'nota'              => $nota,
                            'falta_na_avaliacao' => false,
                        ]);
                    }
                }
            }
        }
    }
}
