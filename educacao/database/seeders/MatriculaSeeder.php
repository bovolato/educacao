<?php

namespace Database\Seeders;

use App\Models\Pessoas\Aluno;
use App\Models\Institucional\{Escola, AnoLetivo};
use App\Models\Academico\{Turma, Matricula, HistoricoMatricula};
use App\Models\User;
use Illuminate\Database\Seeder;

class MatriculaSeeder extends Seeder
{
    public function run(): void
    {
        $escola    = Escola::first();
        $anoAtivo  = AnoLetivo::where('ativo', true)->first();
        $turmas    = Turma::where('escola_id', $escola->id)
            ->where('ano_letivo_id', $anoAtivo->id)
            ->where('status', 'ativa')
            ->get();
        $alunos    = Aluno::where('ativo', true)->get();
        $admin     = User::first();

        if ($turmas->isEmpty()) {
            $this->command->warn('Nenhuma turma encontrada. Execute TurmaSeeder primeiro.');
            return;
        }

        $turmaIndex = 0;
        $alunosPorTurma = ceil($alunos->count() / max($turmas->count(), 1));

        foreach ($alunos as $i => $aluno) {
            // Verificar se já tem matrícula neste ano
            $jaMatriculado = Matricula::where('aluno_id', $aluno->id)
                ->where('ano_letivo_id', $anoAtivo->id)
                ->exists();

            if ($jaMatriculado) {
                continue;
            }

            $turma = $turmas[$turmaIndex % $turmas->count()];

            $numero = 'M' . $anoAtivo->descricao . str_pad($i + 1, 5, '0', STR_PAD_LEFT);

            $matricula = Matricula::create([
                'aluno_id'        => $aluno->id,
                'escola_id'       => $escola->id,
                'ano_letivo_id'   => $anoAtivo->id,
                'turma_id'        => $turma->id,
                'numero_matricula' => $numero,
                'data_matricula'  => $anoAtivo->data_inicio,
                'situacao'        => 'ativa',
                'criado_por'      => $admin?->id,
            ]);

            HistoricoMatricula::create([
                'matricula_id'       => $matricula->id,
                'tipo_movimentacao'  => 'matricula',
                'data_movimentacao'  => $anoAtivo->data_inicio,
                'descricao'          => 'Matrícula realizada para o ano letivo ' . $anoAtivo->descricao,
                'usuario_id'         => $admin?->id,
            ]);

            $turmaIndex++;
        }
    }
}
