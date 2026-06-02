<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DisciplinaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nome'          => 'required|string|max:100',
            'sigla'         => 'nullable|string|max:10',
            'descricao'     => 'nullable|string|max:500',
            'carga_horaria' => 'nullable|integer|min:1',
            'ativo'         => 'boolean',
        ];
    }
}
