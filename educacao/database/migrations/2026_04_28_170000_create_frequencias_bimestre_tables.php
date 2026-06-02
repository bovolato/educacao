<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frequencias_bimestre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('disciplina_id')->nullable()->constrained('disciplinas')->nullOnDelete();
            $table->string('periodo', 3)->index(); // 1B..4B
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->timestamps();

            $table->unique(['professor_id', 'turma_id', 'disciplina_id', 'periodo'], 'freq_bim_unique');
        });

        Schema::create('frequencias_bimestre_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frequencia_bimestre_id')->constrained('frequencias_bimestre')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->unsignedSmallInteger('presencas')->default(0);
            $table->unsignedSmallInteger('faltas')->default(0);
            $table->unsignedSmallInteger('faltas_justificadas')->default(0);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['frequencia_bimestre_id', 'matricula_id'], 'freq_bim_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frequencias_bimestre_itens');
        Schema::dropIfExists('frequencias_bimestre');
    }
};

