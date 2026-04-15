<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SalaRequest;
use App\Models\Institucional\{Escola, Sala};
use Illuminate\Http\Request;

class SalaController extends Controller
{
    public function index(Escola $escola)
    {
        $salas = $escola->salas()->orderBy('nome')->paginate(20);
        return view('admin.escolas.salas.index', compact('escola', 'salas'));
    }

    public function create(Escola $escola)
    {
        return view('admin.escolas.salas.create', compact('escola'));
    }

    public function store(SalaRequest $request, Escola $escola)
    {
        $escola->salas()->create($request->validated());
        return redirect()->route('admin.escolas.salas.index', $escola)
            ->with('success', 'Sala cadastrada com sucesso!');
    }

    public function edit(Sala $sala)
    {
        $sala->load('escola');
        return view('admin.escolas.salas.edit', compact('sala'));
    }

    public function update(SalaRequest $request, Sala $sala)
    {
        $sala->update($request->validated());
        return redirect()->route('admin.escolas.salas.index', $sala->escola_id)
            ->with('success', 'Sala atualizada com sucesso!');
    }

    public function destroy(Sala $sala)
    {
        if ($sala->turmas()->where('status', 'ativa')->exists()) {
            return back()->with('error', 'Não é possível excluir uma sala com turmas ativas vinculadas.');
        }
        $escolaId = $sala->escola_id;
        $sala->delete();
        return redirect()->route('admin.escolas.salas.index', $escolaId)
            ->with('success', 'Sala removida com sucesso!');
    }
}
