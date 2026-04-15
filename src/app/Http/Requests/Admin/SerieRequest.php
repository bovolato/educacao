<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SerieRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'etapa_ensino_id' => 'required|exists:etapas_ensino,id',
            'nome'            => 'required|string|max:100',
            'sigla'           => 'nullable|string|max:10',
            'ordem'           => 'nullable|integer|min:1',
            'ativo'           => 'boolean',
        ];
    }
}
