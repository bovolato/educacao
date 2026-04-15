<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissaoSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissoes = [
            // Institucional
            'municipios.ver', 'municipios.criar', 'municipios.editar', 'municipios.excluir',
            'escolas.ver', 'escolas.criar', 'escolas.editar', 'escolas.excluir',
            'anos-letivos.ver', 'anos-letivos.criar', 'anos-letivos.editar',
            'etapas.ver', 'etapas.criar', 'etapas.editar',
            'series.ver', 'series.criar', 'series.editar',
            'turnos.ver', 'turnos.criar', 'turnos.editar',
            'salas.ver', 'salas.criar', 'salas.editar',

            // Pessoas
            'pessoas.ver', 'pessoas.criar', 'pessoas.editar', 'pessoas.excluir',
            'alunos.ver', 'alunos.criar', 'alunos.editar', 'alunos.excluir',
            'professores.ver', 'professores.criar', 'professores.editar',
            'responsaveis.ver', 'responsaveis.criar', 'responsaveis.editar',
            'funcionarios.ver', 'funcionarios.criar', 'funcionarios.editar',

            // Acadêmico
            'disciplinas.ver', 'disciplinas.criar', 'disciplinas.editar',
            'turmas.ver', 'turmas.criar', 'turmas.editar', 'turmas.excluir',
            'matriculas.ver', 'matriculas.criar', 'matriculas.editar',
            'aulas.ver', 'aulas.criar', 'aulas.editar',
            'frequencias.ver', 'frequencias.lancar',
            'notas.ver', 'notas.lancar', 'notas.editar',
            'avaliacoes.ver', 'avaliacoes.criar', 'avaliacoes.editar',
            'boletins.ver', 'boletins.fechar',

            // Pedagógico
            'planos-ensino.ver', 'planos-ensino.criar', 'planos-ensino.editar',
            'planos-aula.ver', 'planos-aula.criar', 'planos-aula.editar',
            'materiais.ver', 'materiais.criar', 'materiais.editar',
            'tarefas.ver', 'tarefas.criar', 'tarefas.editar',

            // Comunicação
            'avisos.ver', 'avisos.criar', 'avisos.editar', 'avisos.excluir',

            // Documentos
            'documentos.ver', 'documentos.emitir',

            // Relatórios
            'relatorios.municipal', 'relatorios.escola', 'relatorios.turma',

            // Administração
            'usuarios.ver', 'usuarios.criar', 'usuarios.editar', 'usuarios.excluir',
            'configuracoes.ver', 'configuracoes.editar',
            'auditoria.ver',
        ];

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(['name' => $permissao, 'guard_name' => 'web']);
        }

        $perfis = [
            'super_admin' => Permission::all()->pluck('name')->toArray(),

            'secretaria_municipal' => [
                'municipios.ver', 'municipios.editar',
                'escolas.ver', 'escolas.criar', 'escolas.editar',
                'anos-letivos.ver', 'anos-letivos.criar', 'anos-letivos.editar',
                'etapas.ver', 'etapas.criar', 'etapas.editar',
                'series.ver', 'series.criar', 'series.editar',
                'turnos.ver', 'turnos.criar', 'turnos.editar',
                'salas.ver',
                'pessoas.ver', 'pessoas.criar', 'pessoas.editar',
                'alunos.ver', 'alunos.criar', 'alunos.editar',
                'professores.ver', 'professores.criar', 'professores.editar',
                'responsaveis.ver', 'responsaveis.criar', 'responsaveis.editar',
                'funcionarios.ver', 'funcionarios.criar', 'funcionarios.editar',
                'disciplinas.ver', 'disciplinas.criar', 'disciplinas.editar',
                'turmas.ver', 'matriculas.ver',
                'relatorios.municipal', 'relatorios.escola',
                'avisos.ver', 'avisos.criar', 'avisos.editar', 'avisos.excluir',
                'documentos.ver', 'documentos.emitir',
                'usuarios.ver', 'configuracoes.ver', 'auditoria.ver',
            ],

            'gestor_escolar' => [
                'escolas.ver', 'escolas.editar',
                'salas.ver', 'salas.criar', 'salas.editar',
                'pessoas.ver', 'pessoas.criar', 'pessoas.editar',
                'alunos.ver', 'alunos.criar', 'alunos.editar',
                'professores.ver', 'professores.criar', 'professores.editar',
                'responsaveis.ver', 'responsaveis.criar', 'responsaveis.editar',
                'funcionarios.ver', 'funcionarios.criar', 'funcionarios.editar',
                'turmas.ver', 'turmas.criar', 'turmas.editar',
                'matriculas.ver', 'matriculas.criar', 'matriculas.editar',
                'disciplinas.ver',
                'frequencias.ver', 'notas.ver', 'avaliacoes.ver', 'boletins.ver',
                'avisos.ver', 'avisos.criar', 'avisos.editar',
                'documentos.ver', 'documentos.emitir',
                'relatorios.escola', 'relatorios.turma',
            ],

            'secretario_escolar' => [
                'escolas.ver',
                'alunos.ver', 'alunos.criar', 'alunos.editar',
                'responsaveis.ver', 'responsaveis.criar', 'responsaveis.editar',
                'matriculas.ver', 'matriculas.criar', 'matriculas.editar',
                'turmas.ver',
                'documentos.ver', 'documentos.emitir',
                'avisos.ver',
            ],

            'coordenador' => [
                'escolas.ver',
                'alunos.ver', 'professores.ver',
                'turmas.ver', 'disciplinas.ver',
                'frequencias.ver', 'notas.ver', 'avaliacoes.ver', 'boletins.ver', 'boletins.fechar',
                'planos-ensino.ver', 'planos-aula.ver', 'materiais.ver',
                'relatorios.escola', 'relatorios.turma',
                'avisos.ver', 'avisos.criar',
            ],

            'professor' => [
                'turmas.ver', 'disciplinas.ver',
                'aulas.ver', 'aulas.criar', 'aulas.editar',
                'frequencias.ver', 'frequencias.lancar',
                'notas.ver', 'notas.lancar', 'notas.editar',
                'avaliacoes.ver', 'avaliacoes.criar', 'avaliacoes.editar',
                'boletins.ver',
                'planos-ensino.ver', 'planos-ensino.criar', 'planos-ensino.editar',
                'planos-aula.ver', 'planos-aula.criar', 'planos-aula.editar',
                'materiais.ver', 'materiais.criar', 'materiais.editar',
                'tarefas.ver', 'tarefas.criar', 'tarefas.editar',
                'avisos.ver',
                'alunos.ver',
            ],

            'aluno' => [
                'notas.ver', 'frequencias.ver', 'boletins.ver',
                'materiais.ver', 'tarefas.ver', 'avisos.ver', 'documentos.ver',
            ],

            'responsavel' => [
                'notas.ver', 'frequencias.ver', 'boletins.ver',
                'materiais.ver', 'tarefas.ver', 'avisos.ver', 'documentos.ver',
            ],

            'almoxarifado' => [
                'escolas.ver',
            ],

            'transporte' => [
                'escolas.ver', 'alunos.ver',
            ],
        ];

        foreach ($perfis as $nomePerfil => $permissoesDoPerfil) {
            $role = Role::firstOrCreate(['name' => $nomePerfil, 'guard_name' => 'web']);
            $role->syncPermissions($permissoesDoPerfil);
        }
    }
}
