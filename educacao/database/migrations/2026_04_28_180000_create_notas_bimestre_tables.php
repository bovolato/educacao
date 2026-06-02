<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_bimestre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->string('periodo', 3)->index(); // 1B..4B
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->timestamps();

            $table->unique(['professor_id', 'turma_id', 'periodo'], 'notas_bim_unique');
        });

        Schema::create('notas_bimestre_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_bimestre_id')->constrained('notas_bimestre')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->decimal('media_final', 6, 2)->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['nota_bimestre_id', 'matricula_id'], 'nota_bim_item_unique');
        });

        Schema::create('notas_bimestre_itens_disciplinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_bimestre_item_id')->constrained('notas_bimestre_itens')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->cascadeOnDelete();
            $table->decimal('nota', 6, 2)->nullable();
            $table->timestamps();

            $table->unique(['nota_bimestre_item_id', 'disciplina_id'], 'nota_bim_item_disc_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_bimestre_itens_disciplinas');
        Schema::dropIfExists('notas_bimestre_itens');
        Schema::dropIfExists('notas_bimestre');
    }
};

