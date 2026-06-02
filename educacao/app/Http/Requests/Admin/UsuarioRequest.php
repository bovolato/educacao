<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('usuario')?->id;

        return [
            'tipo'         => ['nullable', 'in:admin,secretaria,gestor,coordenador,professor,funcionario,aluno,responsavel'],
            'municipio_id' => 'nullable|exists:municipios,id',
            'escola_id'    => 'nullable|exists:escolas,id',
            'nome'         => 'required|string|max:200',
            'email'        => ['nullable', 'email', 'max:200', Rule::unique('users', 'email')->ignore($id)],
            'username'     => ['nullable', 'string', 'max:100', Rule::unique('users', 'username')->ignore($id)],
            'password'     => $this->isMethod('POST')
                ? ['required', Password::min(8)]
                : ['nullable', Password::min(8)],
            'perfil'       => 'nullable|string|exists:roles,name',
            'ativo'        => 'boolean',
        ];
    }
}
