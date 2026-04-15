<?php

namespace App\Http\Requests\Comunicacao;

use Illuminate\Foundation\Http\FormRequest;

class AvisoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'titulo'       => 'required|string|max:200',
            'mensagem'     => 'required|string',
            'tipo_destino' => 'required|in:geral,escola,turma,perfil',
            'escola_id'    => 'nullable|exists:escolas,id',
            'ativo'        => 'boolean',
        ];
    }
}
