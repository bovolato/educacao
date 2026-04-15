<?php

namespace Database\Seeders;

use App\Models\Comunicacao\Aviso;
use App\Models\Institucional\{Municipio, Escola};
use App\Models\User;
use Illuminate\Database\Seeder;

class AvisoSeeder extends Seeder
{
    public function run(): void
    {
        $municipio = Municipio::first();
        $escola    = Escola::first();
        $admin     = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->first();

        if (!$admin) {
            return;
        }

        $avisos = [
            [
                'titulo'       => 'Início do Ano Letivo 2026',
                'mensagem'     => 'Informamos que o ano letivo de 2026 tem início previsto para o dia 02 de fevereiro. Pedimos que todos os alunos compareçam no primeiro dia com material escolar completo.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
            [
                'titulo'       => 'Reunião de Pais e Mestres',
                'mensagem'     => 'Fica convocada a Reunião de Pais e Mestres para o dia 20 de abril de 2026, às 19h. A presença é obrigatória.',
                'tipo_destino' => 'escola',
                'escola_id'    => $escola->id,
            ],
            [
                'titulo'       => 'Calendário Escolar Atualizado',
                'mensagem'     => 'O calendário escolar do ano letivo 2026 foi atualizado. Acesse a secretaria para maiores informações.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
            [
                'titulo'       => 'Campanha de Vacinação',
                'mensagem'     => 'A Secretaria Municipal de Saúde realizará campanha de vacinação nas escolas municipais no próximo mês.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
            [
                'titulo'       => 'Entrega de Materiais Escolares',
                'mensagem'     => 'Os kits de material escolar estarão disponíveis para retirada na secretaria escolar a partir do dia 05 de fevereiro.',
                'tipo_destino' => 'escola',
                'escola_id'    => $escola->id,
            ],
            [
                'titulo'       => 'Semana Cultural 2026',
                'mensagem'     => 'A Semana Cultural será realizada de 12 a 16 de maio. Professores devem preparar apresentações com suas turmas.',
                'tipo_destino' => 'escola',
                'escola_id'    => $escola->id,
            ],
            [
                'titulo'       => 'Prazo para Rematrícula',
                'mensagem'     => 'O prazo para rematrícula para o ano letivo de 2027 encerra em 30 de novembro de 2026. Procure a secretaria.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
            [
                'titulo'       => 'Recesso de Julho',
                'mensagem'     => 'O recesso escolar de julho será de 07 a 25 de julho de 2026. As aulas retornam no dia 28 de julho.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
            [
                'titulo'       => 'Projeto Leitura nas Escolas',
                'mensagem'     => 'A Secretaria Municipal de Educação lança o Projeto Leitura nas Escolas. Todas as turmas terão 30 minutos semanais dedicados à leitura livre.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
            [
                'titulo'       => 'Aviso de Manutenção no Sistema',
                'mensagem'     => 'O sistema SIGEM passará por manutenção programada no próximo sábado das 8h às 12h. Durante este período, o acesso estará indisponível.',
                'tipo_destino' => 'geral',
                'escola_id'    => null,
            ],
        ];

        foreach ($avisos as $dados) {
            \App\Models\Comunicacao\Aviso::create(array_merge($dados, [
                'municipio_id' => $municipio->id,
                'usuario_id'   => $admin->id,
                'publicado_em' => now()->subDays(rand(0, 30)),
                'ativo'        => true,
            ]));
        }
    }
}
