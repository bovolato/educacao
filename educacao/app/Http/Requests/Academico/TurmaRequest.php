<?php

namespace App\Http\Requests\Academico;

use Illuminate\Foundation\Http\FormRequest;

class TurmaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'escola_id'     => 'required|exists:escolas,id',
            'ano_letivo_id' => 'required|exists:anos_letivos,id',
            'serie_id'      => 'required|exists:series,id',
            'turno_id'      => 'required|exists:turnos,id',
            'sala_id'       => 'nullable|exists:salas,id',
            'nome'          => 'required|string|max:50',
            'codigo'        => 'nullable|string|max:20',
            'capacidade'    => 'nullable|integer|min:1|max:50',
            'polivalente'   => 'nullable|boolean',
            'status'        => 'required|in:ativa,encerrada,suspensa',
        ];
    }
}
