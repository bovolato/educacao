<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('municipio')?->id;

        return [
            'nome'        => 'required|string|max:200',
            'uf'          => 'required|string|size:2',
            'codigo_ibge' => ['nullable', 'string', 'max:10', Rule::unique('municipios', 'codigo_ibge')->ignore($id)],
            'cnpj'        => 'nullable|string|max:18',
            'telefone'    => 'nullable|string|max:20',
            'email'       => 'nullable|email|max:200',
            'logradouro'  => 'nullable|string|max:200',
            'numero'      => 'nullable|string|max:20',
            'bairro'      => 'nullable|string|max:100',
            'cidade'      => 'nullable|string|max:100',
            'cep'         => 'nullable|string|max:9',
            'ativo'       => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'        => 'nome do município',
            'uf'          => 'UF',
            'codigo_ibge' => 'código IBGE',
        ];
    }
}
