<?php

namespace Database\Seeders;

use App\Models\Pessoas\Aluno;
use App\Models\User;
use App\Models\Institucional\Municipio;
use Database\Seeders\Concerns\GeraPessoaFake;
use Illuminate\Database\Seeder;

class AlunoSeeder extends Seeder
{
    use GeraPessoaFake;

    public function run(): void
    {
        $municipioIds = Municipio::where('ativo', true)->orderBy('id')->pluck('id')->values();

        for ($i = 0; $i < 50; $i++) {
            $sexo = $i % 2 === 0 ? 'M' : 'F';
            $nome = $this->nomeCompleto($sexo);

            $usuario = User::create([
                'tipo'            => 'aluno',
                'nome'            => $nome,
                'cpf'             => $this->gerarCpf(),
                'data_nascimento' => $this->dataNascimento(2005, 2018),
                'sexo'            => $sexo,
                'nome_mae'        => $this->nomeCompleto('F'),
                'nome_pai'        => $this->nomeCompleto('M'),
                'naturalidade'    => 'Município Exemplo',
                'naturalidade_uf' => 'SP',
                'nacionalidade'   => 'Brasileira',
                'ativo'           => true,
            ]);

            $emailLocal = strtolower(explode(' ', $nome)[0]) . rand(10, 99);
            $this->criarContatosEndereco($usuario, $emailLocal, $i);

            $municipioId = $municipioIds->isNotEmpty()
                ? $municipioIds[$i % $municipioIds->count()]
                : null;

            Aluno::create([
                'user_id'         => $usuario->id,
                'municipio_id'    => $municipioId,
                'ra'              => 'RA' . str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT),
                'codigo_aluno'    => 'AL' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'nis'             => (string) rand(10000000000, 99999999999),
                'necessidades_especiais' => $i % 15 === 0,
                'descricao_necessidades' => $i % 15 === 0 ? 'TDAH leve' : null,
                'usa_transporte'  => $i % 4 === 0,
                'ativo'           => true,
            ]);
        }
    }
}
