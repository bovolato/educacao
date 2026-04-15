<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('tipo')->nullable();
            $table->date('data_avaliacao')->nullable();
            $table->decimal('valor', 5, 2)->default(10.00);
            $table->string('periodo')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();
        });

        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avaliacao_id')->constrained('avaliacoes')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->decimal('nota', 5, 2)->nullable();
            $table->boolean('falta_na_avaliacao')->default(false);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['avaliacao_id', 'aluno_id']);
        });

        Schema::create('boletins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->string('periodo');
            $table->decimal('media', 5, 2)->nullable();
            $table->integer('faltas')->default(0);
            $table->string('situacao')->nullable();
            $table->timestamp('fechado_em')->nullable();
            $table->timestamps();

            $table->unique(['matricula_id', 'disciplina_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletins');
        Schema::dropIfExists('notas');
        Schema::dropIfExists('avaliacoes');
    }
};
