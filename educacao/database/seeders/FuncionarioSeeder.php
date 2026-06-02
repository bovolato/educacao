<?php

namespace Database\Seeders;

use App\Models\Pessoas\Funcionario;
use App\Models\Institucional\Escola;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FuncionarioSeeder extends Seeder
{
    public function run(): void
    {
        $escola      = Escola::first();
        $municipioId = $escola->municipio_id;

        $funcionarios = [
            ['nome' => 'Maria José da Silva', 'cargo' => 'Secretária Escolar', 'setor' => 'Secretaria', 'perfil' => 'secretario_escolar'],
            ['nome' => 'Roberto Carlos Lima', 'cargo' => 'Coordenador Pedagógico', 'setor' => 'Pedagógico', 'perfil' => 'coordenador'],
            ['nome' => 'Sônia Regina Pires', 'cargo' => 'Auxiliar Administrativo', 'setor' => 'Administração', 'perfil' => null],
            ['nome' => 'Antônio Ferreira Neto', 'cargo' => 'Inspetor de Alunos', 'setor' => 'Supervisão', 'perfil' => null],
            ['nome' => 'Cleide Aparecida Matos', 'cargo' => 'Auxiliar de Limpeza', 'setor' => 'Serviços Gerais', 'perfil' => null],
            ['nome' => 'Jair Pereira dos Santos', 'cargo' => 'Porteiro', 'setor' => 'Portaria', 'perfil' => null],
            ['nome' => 'Dirce Fátima Ramos', 'cargo' => 'Merendeira', 'setor' => 'Cozinha', 'perfil' => null],
            ['nome' => 'Geraldo Mendes Filho', 'cargo' => 'Auxiliar de Serviços', 'setor' => 'Manutenção', 'perfil' => null],
        ];

        foreach ($funcionarios as $i => $dados) {
            $temLogin = (bool) $dados['perfil'];
            $slug     = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $dados['nome'])[0])) . ($i + 1);

            $user = User::create([
                'tipo'            => 'funcionario',
                'municipio_id'    => $municipioId,
                'escola_id'       => $escola->id,
                'nome'            => $dados['nome'],
                'cpf'             => $this->gerarCpf(),
                'data_nascimento' => sprintf('%04d-%02d-%02d', rand(1968, 1985), rand(1, 12), rand(1, 28)),
                'sexo'            => in_array($i, [0, 2, 4, 6]) ? 'F' : 'M',
                'email'           => $temLogin ? $slug . '@escola.edu.br' : null,
                'username'        => $temLogin ? $slug : null,
                'password'        => $temLogin ? Hash::make('escola@2026') : null,
                'ativo'           => true,
            ]);

            $user->contatos()->create([
                'tipo'      => 'celular',
                'valor'     => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'principal' => true,
            ]);
            $user->enderecos()->create([
                'logradouro' => 'Rua dos Servidores',
                'numero'     => (string) (($i + 1) * 5),
                'bairro'     => 'Centro',
                'cidade'     => 'Município Exemplo',
                'uf'         => 'SP',
                'cep'        => '01234-00' . ($i + 1),
                'principal'  => true,
            ]);

            if ($temLogin) {
                $user->assignRole($dados['perfil']);
            }

            Funcionario::create([
                'user_id'             => $user->id,
                'escola_id'           => $escola->id,
                'matricula_funcional' => 'FUNC' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'cargo'               => $dados['cargo'],
                'setor'               => $dados['setor'],
                'data_admissao'       => sprintf('%04d-%02d-%02d', rand(2005, 2020), rand(1, 12), rand(1, 28)),
                'ativo'               => true,
            ]);
        }

        // Criar gestor escolar (com login)
        $gestor = User::create([
            'tipo'            => 'gestor',
            'municipio_id'    => $municipioId,
            'escola_id'       => $escola->id,
            'nome'            => 'José Augusto Fonseca',
            'cpf'             => $this->gerarCpf(),
            'data_nascimento' => '1972-05-15',
            'sexo'            => 'M',
            'email'           => 'diretor@escola.edu.br',
            'username'        => 'diretor',
            'password'        => Hash::make('escola@2026'),
            'ativo'           => true,
        ]);
        $gestor->assignRole('gestor_escolar');

        Funcionario::create([
            'user_id'             => $gestor->id,
            'escola_id'           => $escola->id,
            'matricula_funcional' => 'DIR0001',
            'cargo'               => 'Diretor Escolar',
            'setor'               => 'Direção',
            'data_admissao'       => '2015-02-01',
            'ativo'               => true,
        ]);
    }

    private function gerarCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) $n[] = rand(0, 9);
        $d1 = 0;
        for ($i = 0; $i < 9; $i++) $d1 += $n[$i] * (10 - $i);
        $d1 = ($d1 % 11 < 2) ? 0 : 11 - ($d1 % 11);
        $n[] = $d1;
        $d2 = 0;
        for ($i = 0; $i < 10; $i++) $d2 += $n[$i] * (11 - $i);
        $d2 = ($d2 % 11 < 2) ? 0 : 11 - ($d2 % 11);
        $n[] = $d2;
        return sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', ...$n);
    }
}
