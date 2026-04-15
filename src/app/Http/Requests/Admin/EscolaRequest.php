<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EscolaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('escola')?->id;

        return [
            'municipio_id'  => 'required|exists:municipios,id',
            'nome'          => 'required|string|max:200',
            'codigo'        => ['nullable', 'string', 'max:30', Rule::unique('escolas', 'codigo')->ignore($id)],
            'inep'          => ['nullable', 'string', 'max:20', Rule::unique('escolas', 'inep')->ignore($id)],
            'cnpj'          => 'nullable|string|max:18',
            'telefone'      => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:200',
            'logradouro'    => 'nullable|string|max:200',
            'numero'        => 'nullable|string|max:20',
            'complemento'   => 'nullable|string|max:100',
            'bairro'        => 'nullable|string|max:100',
            'cidade'        => 'nullable|string|max:100',
            'uf'            => 'nullable|string|size:2',
            'cep'           => 'nullable|string|max:9',
            'diretor_nome'  => 'nullable|string|max:200',
            'status'        => 'required|in:ativa,inativa,em_obras',
        ];
    }

    public function attributes(): array
    {
        return [
            'municipio_id' => 'município',
            'nome'         => 'nome da escola',
            'status'       => 'status',
        ];
    }
}
