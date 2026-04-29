<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anotacoes_professor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->string('periodo', 3)->index(); // 1B..4B
            $table->string('assunto', 180);
            $table->text('texto');
            $table->timestamps();

            $table->index(['turma_id', 'matricula_id']);
            $table->index(['professor_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anotacoes_professor');
    }
};

