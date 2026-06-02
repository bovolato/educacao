<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AnoLetivoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'municipio_id' => 'required|exists:municipios,id',
            'descricao'    => 'required|string|max:20',
            'data_inicio'  => 'required|date',
            'data_fim'     => 'required|date|after:data_inicio',
            'ativo'        => 'boolean',
        ];
    }
}
