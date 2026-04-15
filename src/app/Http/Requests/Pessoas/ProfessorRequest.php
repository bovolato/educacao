<?php

namespace App\Http\Requests\Pessoas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $pessoaId = $this->route('professor')?->pessoa_id;
        return [
            'escola_id'              => 'required|exists:escolas,id',
            'nome'                   => 'required|string|max:200',
            'cpf'                    => ['nullable', 'string', 'max:14', Rule::unique('pessoas', 'cpf')->ignore($pessoaId)],
            'data_nascimento'        => 'nullable|date',
            'sexo'                   => 'nullable|in:M,F,O',
            'telefone'               => 'nullable|string|max:20',
            'email_contato'          => 'nullable|email|max:200',
            'matricula_funcional'    => 'nullable|string|max:30',
            'formacao'               => 'nullable|string|max:200',
            'registro_profissional'  => 'nullable|string|max:50',
            'data_admissao'          => 'nullable|date',
            'ativo'                  => 'boolean',
        ];
    }
}
