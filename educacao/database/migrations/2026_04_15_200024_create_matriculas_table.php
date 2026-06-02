<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->foreignId('ano_letivo_id')->constrained('anos_letivos')->cascadeOnDelete();
            $table->foreignId('turma_id')->nullable()->constrained('turmas')->nullOnDelete();
            $table->string('numero_matricula', 30)->unique();
            $table->date('data_matricula');
            $table->enum('situacao', ['ativa', 'transferido', 'cancelada', 'concluida', 'falecido'])->default('ativa');
            $table->string('origem')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['aluno_id', 'escola_id', 'ano_letivo_id']);
        });

        Schema::create('historico_matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->enum('tipo_movimentacao', ['matricula', 'rematricula', 'transferencia', 'cancelamento', 'conclusao', 'reenturmacao']);
            $table->date('data_movimentacao');
            $table->text('descricao')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_matriculas');
        Schema::dropIfExists('matriculas');
    }
};
