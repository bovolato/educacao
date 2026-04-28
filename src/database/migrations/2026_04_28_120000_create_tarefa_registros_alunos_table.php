<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefa_registros_alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarefa_id')->constrained('tarefas')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('professores')->cascadeOnDelete();
            $table->enum('status', ['pendente', 'fez', 'nao_fez', 'entregue', 'nao_entregue'])->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['tarefa_id', 'matricula_id'], 'tarefa_matricula_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefa_registros_alunos');
    }
};

