<?php

namespace Database\Seeders;

use App\Models\Academico\{Turma, Aula, ConteudoAula, Frequencia, Matricula};
use Illuminate\Database\Seeder;

class AulaSeeder extends Seeder
{
    private array $conteudos = [
        'Introdução ao tema',
        'Revisão do conteúdo anterior',
        'Exercícios práticos',
        'Leitura e interpretação de texto',
        'Resolução de problemas',
        'Atividade em grupo',
        'Avaliação formativa',
        'Correção de exercícios',
        'Aprofundamento do tema',
        'Produção textual',
    ];

    public function run(): void
    {
        $turmas = Turma::where('status', 'ativa')
            ->with(['disciplinas', 'professores', 'matriculasAtivas.aluno'])
            ->get();

        foreach ($turmas as $turma) {
            $disciplinas = $turma->disciplinas;
            if ($disciplinas->isEmpty()) {
                continue;
            }

            // 15 aulas por turma (nas últimas 3 semanas)
            for ($d = 14; $d >= 0; $d--) {
                // Somente dias úteis
                $data = now()->subDays($d);
                if (in_array($data->dayOfWeek, [0, 6])) {
                    continue;
                }

                $disciplina = $disciplinas[$d % $disciplinas->count()];

                // Buscar professor vinculado
                $profPivot = \DB::table('turma_professores')
                    ->where('turma_id', $turma->id)
                    ->where('disciplina_id', $disciplina->id)
                    ->first();

                if (!$profPivot) {
                    continue;
                }

                $aula = Aula::create([
                    'turma_id'      => $turma->id,
                    'disciplina_id' => $disciplina->id,
                    'professor_id'  => $profPivot->professor_id,
                    'data_aula'     => $data->toDateString(),
                    'hora_inicio'   => '07:30',
                    'hora_fim'      => '09:10',
                    'status'        => 'realizada',
                ]);

                ConteudoAula::create([
                    'aula_id'           => $aula->id,
                    'professor_id'      => $profPivot->professor_id,
                    'titulo'            => $this->conteudos[$d % count($this->conteudos)],
                    'descricao'         => 'Aula realizada conforme planejamento pedagógico.',
                    'material_utilizado' => 'Livro didático e quadro branco',
                    'tarefa_passada'    => $d % 5 === 0,
                ]);

                // Lançar frequência para os alunos matriculados
                $matriculas = $turma->matriculasAtivas()->with('aluno')->get();
                foreach ($matriculas as $matricula) {
                    $situacao = rand(1, 10) <= 8 ? 'presente' : 'falta';
                    Frequencia::create([
                        'matricula_id' => $matricula->id,
                        'aula_id'      => $aula->id,
                        'aluno_id'     => $matricula->aluno_id,
                        'situacao'     => $situacao,
                    ]);
                }
            }
        }
    }
}
