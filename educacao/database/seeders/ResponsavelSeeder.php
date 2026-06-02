<?php

namespace Database\Seeders;

use App\Models\Pessoas\{Responsavel, Aluno};
use App\Models\User;
use Database\Seeders\Concerns\GeraPessoaFake;
use Illuminate\Database\Seeder;

class ResponsavelSeeder extends Seeder
{
    use GeraPessoaFake;

    public function run(): void
    {
        $alunos = Aluno::all();

        // Criar 35 responsáveis (cada um com usuário tipo responsavel)
        $responsaveisIds = [];
        for ($i = 0; $i < 35; $i++) {
            $sexo = $i % 2 === 0 ? 'F' : 'M';
            $nome = $this->nomeCompleto($sexo);

            $usuario = User::create([
                'tipo'            => 'responsavel',
                'nome'            => $nome,
                'cpf'             => $this->gerarCpf(),
                'data_nascimento' => $this->dataNascimento(1970, 1994),
                'sexo'            => $sexo,
                'naturalidade'    => 'Município Exemplo',
                'naturalidade_uf' => 'SP',
                'nacionalidade'   => 'Brasileira',
                'ativo'           => true,
            ]);

            $emailLocal = strtolower(explode(' ', $nome)[0]) . rand(10, 99);
            $this->criarContatosEndereco($usuario, $emailLocal, $i);

            $responsavel = Responsavel::create([
                'user_id'            => $usuario->id,
                'tipo_responsavel'   => collect(['Mãe', 'Pai', 'Avó', 'Avô', 'Tio(a)', 'Tutor(a)'])->random(),
                'responsavel_legal'  => true,
                'financeiro'         => (bool) rand(0, 1),
                'recebe_notificacao' => true,
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

            $aluno->responsaveis()->syncWithoutDetaching([
                $resp1Id => [
                    'grau_parentesco'       => 'Mãe',
                    'responsavel_principal' => true,
                    'retira_aluno'          => true,
                    'recebe_boletim'        => true,
                ],
            ]);

            // Segundo responsável para metade dos alunos
            if ($respIndex % 2 === 0 && isset($responsaveisIds[$respIndex % count($responsaveisIds)])) {
                $resp2Id = $responsaveisIds[$respIndex % count($responsaveisIds)];
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
