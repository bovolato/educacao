<?php

namespace App\Http\Requests\Pessoas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlunoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $pessoaId = $this->route('aluno')?->pessoa_id;

        return [
            // Pessoa
            'nome'            => 'required|string|max:200',
            'cpf'             => ['nullable', 'string', 'max:14', Rule::unique('pessoas', 'cpf')->ignore($pessoaId)],
            'data_nascimento' => 'nullable|date',
            'sexo'            => 'nullable|in:M,F,O',
            'nome_mae'        => 'nullable|string|max:200',
            'nome_pai'        => 'nullable|string|max:200',
            'naturalidade'    => 'nullable|string|max:100',
            'naturalidade_uf' => 'nullable|string|size:2',
            // Contato
            'telefone'        => 'nullable|string|max:20',
            'email_contato'   => 'nullable|email|max:200',
            // Endereço
            'logradouro'      => 'nullable|string|max:200',
            'numero'          => 'nullable|string|max:20',
            'complemento'     => 'nullable|string|max:100',
            'bairro'          => 'nullable|string|max:100',
            'cidade'          => 'nullable|string|max:100',
            'uf'              => 'nullable|string|size:2',
            'cep'             => 'nullable|string|max:9',
            // Aluno
            'ra'              => 'nullable|string|max:30',
            'codigo_aluno'    => 'nullable|string|max:30',
            'nis'             => 'nullable|string|max:20',
            'necessidades_especiais'  => 'boolean',
            'descricao_necessidades'  => 'nullable|string|max:500',
            'usa_transporte'  => 'boolean',
            'ativo'           => 'boolean',
        ];
    }
}
