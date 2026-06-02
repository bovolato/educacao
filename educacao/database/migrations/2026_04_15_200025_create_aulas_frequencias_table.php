<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->date('data_aula');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->enum('status', ['prevista', 'realizada', 'cancelada'])->default('prevista');
            $table->timestamps();
        });

        Schema::create('conteudos_aula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->text('material_utilizado')->nullable();
            $table->boolean('tarefa_passada')->default(false);
            $table->timestamps();
        });

        Schema::create('frequencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->enum('situacao', ['presente', 'falta', 'justificada', 'atraso'])->default('presente');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['matricula_id', 'aula_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frequencias');
        Schema::dropIfExists('conteudos_aula');
        Schema::dropIfExists('aulas');
    }
};
