<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class MatriculaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'aluno_id'         => 'required|exists:alunos,id',
            'escola_id'        => 'required|exists:escolas,id',
            'ano_letivo_id'    => 'required|exists:anos_letivos,id',
            'turma_id'         => 'required|exists:turmas,id',
            'numero_matricula' => 'nullable|string|max:30',
        ];

        if ($this->route('matricula')) {
            $rules['situacao'] = 'required|in:ativa,transferida,evadida,concluida,cancelada';
        }

        return $rules;
    }
}
