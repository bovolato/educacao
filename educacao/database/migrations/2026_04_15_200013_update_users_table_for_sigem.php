<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tipo de usuário (perfil primário p/ filtro/exibição; permissões via roles)
            $table->enum('tipo', [
                'admin', 'secretaria', 'gestor', 'coordenador',
                'professor', 'funcionario', 'aluno', 'responsavel',
            ])->nullable()->after('id');

            // Vínculo institucional
            $table->foreignId('municipio_id')->nullable()->after('tipo')->constrained('municipios')->nullOnDelete();
            $table->foreignId('escola_id')->nullable()->after('municipio_id')->constrained('escolas')->nullOnDelete();

            // Dados pessoais comuns (antiga tabela pessoas)
            $table->string('nome_social')->nullable()->after('nome');
            $table->string('cpf', 14)->nullable()->unique()->after('nome_social');
            $table->string('rg', 20)->nullable()->after('cpf');
            $table->string('rg_orgao_emissor', 20)->nullable()->after('rg');
            $table->date('data_nascimento')->nullable()->after('rg_orgao_emissor');
            $table->enum('sexo', ['M', 'F', 'O'])->nullable()->after('data_nascimento');
            $table->string('estado_civil')->nullable()->after('sexo');
            $table->string('nome_mae')->nullable()->after('estado_civil');
            $table->string('nome_pai')->nullable()->after('nome_mae');
            $table->string('naturalidade')->nullable()->after('nome_pai');
            $table->char('naturalidade_uf', 2)->nullable()->after('naturalidade');
            $table->string('nacionalidade')->default('Brasileira')->after('naturalidade_uf');
            $table->string('foto')->nullable()->after('nacionalidade');
            $table->text('observacoes')->nullable()->after('foto');

            // Acesso
            $table->string('username', 60)->nullable()->unique()->after('email');
            $table->timestamp('ultimo_login_em')->nullable()->after('remember_token');
            $table->string('ultimo_ip', 45)->nullable()->after('ultimo_login_em');
            $table->boolean('ativo')->default(true)->after('ultimo_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['escola_id']);
            $table->dropColumn([
                'tipo', 'municipio_id', 'escola_id',
                'nome_social', 'cpf', 'rg', 'rg_orgao_emissor', 'data_nascimento',
                'sexo', 'estado_civil', 'nome_mae', 'nome_pai', 'naturalidade',
                'naturalidade_uf', 'nacionalidade', 'foto', 'observacoes',
                'username', 'ultimo_login_em', 'ultimo_ip', 'ativo',
            ]);
        });
    }
};
