<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos_ensino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->foreignId('ano_letivo_id')->constrained('anos_letivos')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('objetivos')->nullable();
            $table->text('metodologia')->nullable();
            $table->text('criterios_avaliacao')->nullable();
            $table->timestamps();
        });

        Schema::create('planos_aula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->date('data_prevista')->nullable();
            $table->string('titulo');
            $table->text('objetivos')->nullable();
            $table->text('conteudo_previsto')->nullable();
            $table->text('recursos')->nullable();
            $table->timestamps();
        });

        Schema::create('materiais_didaticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->nullable()->constrained('disciplinas')->nullOnDelete();
            $table->foreignId('turma_id')->nullable()->constrained('turmas')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('arquivo')->nullable();
            $table->string('link')->nullable();
            $table->boolean('visivel_aluno')->default(true);
            $table->timestamps();
        });

        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data_postagem')->nullable();
            $table->date('data_entrega')->nullable();
            $table->decimal('valor', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
        Schema::dropIfExists('materiais_didaticos');
        Schema::dropIfExists('planos_aula');
        Schema::dropIfExists('planos_ensino');
    }
};
