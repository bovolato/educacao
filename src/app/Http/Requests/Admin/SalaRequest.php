<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SalaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'       => 'required|string|max:100',
            'codigo'     => 'nullable|string|max:20',
            'capacidade' => 'nullable|integer|min:1|max:200',
            'tipo'       => 'nullable|string|max:50',
            'ativo'      => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome da sala',
        ];
    }
}
