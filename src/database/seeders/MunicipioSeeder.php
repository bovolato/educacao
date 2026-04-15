<?php

namespace Database\Seeders;

use App\Models\Institucional\{Municipio, AnoLetivo, EtapaEnsino, Serie, Turno, Escola, Sala};
use Illuminate\Database\Seeder;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $municipio = Municipio::create([
            'nome'        => 'Município Exemplo',
            'uf'          => 'SP',
            'codigo_ibge' => '3550308',
            'cnpj'        => '00.000.000/0000-00',
            'telefone'    => '(11) 0000-0000',
            'email'       => 'educacao@municipio.sp.gov.br',
            'logradouro'  => 'Praça da Sé',
            'numero'      => 'S/N',
            'bairro'      => 'Centro',
            'cidade'      => 'Município Exemplo',
            'cep'         => '01001-000',
            'ativo'       => true,
        ]);

        $anoLetivo = AnoLetivo::create([
            'municipio_id' => $municipio->id,
            'descricao'    => '2026',
            'data_inicio'  => '2026-02-01',
            'data_fim'     => '2026-12-15',
            'ativo'        => true,
            'encerrado'    => false,
        ]);

        $turnos = [
            ['nome' => 'Manhã', 'hora_inicio' => '07:00', 'hora_fim' => '12:00'],
            ['nome' => 'Tarde', 'hora_inicio' => '13:00', 'hora_fim' => '18:00'],
            ['nome' => 'Noite', 'hora_inicio' => '19:00', 'hora_fim' => '22:30'],
            ['nome' => 'Integral', 'hora_inicio' => '07:00', 'hora_fim' => '17:00'],
        ];

        foreach ($turnos as $turno) {
            Turno::create(array_merge($turno, ['municipio_id' => $municipio->id, 'ativo' => true]));
        }

        $etapas = [
            ['nome' => 'Educação Infantil', 'ordem' => 1, 'series' => [
                ['nome' => 'Berçário I', 'sigla' => 'BI', 'ordem' => 1],
                ['nome' => 'Berçário II', 'sigla' => 'BII', 'ordem' => 2],
                ['nome' => 'Maternal I', 'sigla' => 'MI', 'ordem' => 3],
                ['nome' => 'Maternal II', 'sigla' => 'MII', 'ordem' => 4],
                ['nome' => 'Pré I', 'sigla' => 'PI', 'ordem' => 5],
                ['nome' => 'Pré II', 'sigla' => 'PII', 'ordem' => 6],
            ]],
            ['nome' => 'Ensino Fundamental I', 'ordem' => 2, 'series' => [
                ['nome' => '1º Ano', 'sigla' => '1A', 'ordem' => 1],
                ['nome' => '2º Ano', 'sigla' => '2A', 'ordem' => 2],
                ['nome' => '3º Ano', 'sigla' => '3A', 'ordem' => 3],
                ['nome' => '4º Ano', 'sigla' => '4A', 'ordem' => 4],
                ['nome' => '5º Ano', 'sigla' => '5A', 'ordem' => 5],
            ]],
            ['nome' => 'Ensino Fundamental II', 'ordem' => 3, 'series' => [
                ['nome' => '6º Ano', 'sigla' => '6A', 'ordem' => 1],
                ['nome' => '7º Ano', 'sigla' => '7A', 'ordem' => 2],
                ['nome' => '8º Ano', 'sigla' => '8A', 'ordem' => 3],
                ['nome' => '9º Ano', 'sigla' => '9A', 'ordem' => 4],
            ]],
        ];

        foreach ($etapas as $etapaDados) {
            $seriesDados = $etapaDados['series'];
            unset($etapaDados['series']);

            $etapa = EtapaEnsino::create(array_merge($etapaDados, ['municipio_id' => $municipio->id, 'ativo' => true]));

            foreach ($seriesDados as $serie) {
                Serie::create(array_merge($serie, ['etapa_ensino_id' => $etapa->id, 'ativo' => true]));
            }
        }

        $escola = Escola::create([
            'municipio_id' => $municipio->id,
            'nome'         => 'Escola Municipal João da Silva',
            'codigo'       => 'EMJS001',
            'inep'         => '12345678',
            'telefone'     => '(11) 3333-0000',
            'email'        => 'emjoaosilva@municipio.sp.gov.br',
            'logradouro'   => 'Rua das Flores',
            'numero'       => '100',
            'bairro'       => 'Jardim das Rosas',
            'cidade'       => 'Município Exemplo',
            'uf'           => 'SP',
            'cep'          => '01234-000',
            'diretor_nome' => 'Maria das Graças',
            'status'       => 'ativa',
        ]);

        Sala::create(['escola_id' => $escola->id, 'nome' => 'Sala 01', 'codigo' => 'S01', 'capacidade' => 35, 'tipo' => 'aula', 'ativo' => true]);
        Sala::create(['escola_id' => $escola->id, 'nome' => 'Sala 02', 'codigo' => 'S02', 'capacidade' => 35, 'tipo' => 'aula', 'ativo' => true]);
        Sala::create(['escola_id' => $escola->id, 'nome' => 'Laboratório de Informática', 'codigo' => 'LAB', 'capacidade' => 30, 'tipo' => 'laboratorio', 'ativo' => true]);
    }
}
