<?php

namespace Database\Seeders;

use App\Models\Pessoas\{Pessoa, Responsavel, Aluno};
use Illuminate\Database\Seeder;

class ResponsavelSeeder extends Seeder
{
    public function run(): void
    {
        $alunos = Aluno::with('pessoa')->get();

        // Usar pessoas adultas (nascidas antes de 1995) como responsáveis
        $pessoas = Pessoa::where('data_nascimento', '<', '1995-01-01')
            ->doesntHave('responsavel')
            ->doesntHave('aluno')
            ->limit(40)
            ->get();

        $responsaveisIds = [];

        foreach ($pessoas->take(35) as $pessoa) {
            $responsavel = Responsavel::create([
                'pessoa_id'           => $pessoa->id,
                'tipo_responsavel'    => collect(['Mãe', 'Pai', 'Avó', 'Avô', 'Tio(a)', 'Tutor(a)'])->random(),
                'responsavel_legal'   => true,
                'financeiro'          => rand(0, 1),
                'recebe_notificacao'  => true,
            ]);
            $responsaveisIds[] = $responsavel->id;
        }

        // Vincular responsáveis aos alunos (cada aluno ganha 1-2 responsáveis)
        $respIndex = 0;
        foreach ($alunos as $aluno) {
            if (empty($responsaveisIds)) {
                break;
            }

            $resp1Id = $responsaveisIds[$respIndex % count($responsaveisIds)];
            $respIndex++;

            // Evitar duplicata
            $aluno->responsaveis()->syncWithoutDetaching([
                $resp1Id => [
                    'grau_parentesco'       => 'Mãe',
                    'responsavel_principal' => true,
                    'retira_aluno'          => true,
                    'recebe_boletim'        => true,
                ],
            ]);

            // Segundo responsável para metade dos alunos
            if ($respIndex % 2 === 0 && isset($responsaveisIds[($respIndex) % count($responsaveisIds)])) {
                $resp2Id = $responsaveisIds[($respIndex) % count($responsaveisIds)];
                if ($resp2Id !== $resp1Id) {
                    $aluno->responsaveis()->syncWithoutDetaching([
                        $resp2Id => [
                            'grau_parentesco'       => 'Pai',
                            'responsavel_principal' => false,
                            'retira_aluno'          => true,
                            'recebe_boletim'        => false,
                        ],
                    ]);
                }
            }
        }
    }
}
