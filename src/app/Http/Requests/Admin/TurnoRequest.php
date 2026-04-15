<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TurnoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'municipio_id' => 'nullable|exists:municipios,id',
            'nome'         => 'required|string|max:50',
            'hora_inicio'  => 'nullable|string|max:5',
            'hora_fim'     => 'nullable|string|max:5',
            'ativo'        => 'boolean',
        ];
    }
}
