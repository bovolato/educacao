<?php

namespace App\Http\Requests\Pessoas;

use Illuminate\Foundation\Http\FormRequest;

class ResponsavelRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nome'               => 'required|string|max:200',
            'cpf'                => 'nullable|string|max:14',
            'data_nascimento'    => 'nullable|date',
            'sexo'               => 'nullable|in:M,F,O',
            'telefone'           => 'nullable|string|max:20',
            'email_contato'      => 'nullable|email|max:200',
            'tipo_responsavel'   => 'required|string|max:50',
            'responsavel_legal'  => 'boolean',
            'financeiro'         => 'boolean',
            'recebe_notificacao' => 'boolean',
        ];
    }
}
