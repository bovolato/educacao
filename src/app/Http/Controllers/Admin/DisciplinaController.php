<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DisciplinaRequest;
use App\Models\Academico\Disciplina;
use App\Models\Institucional\Municipio;
use Illuminate\Http\Request;

class DisciplinaController extends Controller
{
    public function index(Request $request)
    {
        $disciplinas = Disciplina::with('municipio')
            ->when($request->filled('busca'), fn($q) => $q->where('nome', 'like', '%' . $request->busca . '%')
                ->orWhere('sigla', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('ativo'), fn($q) => $q->where('ativo', $request->ativo === '1'))
            ->orderBy('nome')
            ->paginate(10)->withQueryString();

        return view('admin.disciplinas.index', compact('disciplinas'));
    }

    public function create()
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.disciplinas.create', compact('municipios'));
    }

    public function store(DisciplinaRequest $request)
    {
        Disciplina::create($request->validated());
        return redirect()->route('admin.disciplinas.index')->with('success', 'Disciplina cadastrada!');
    }

    public function show(Disciplina $disciplina)
    {
        return view('admin.disciplinas.show', compact('disciplina'));
    }

    public function edit(Disciplina $disciplina)
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.disciplinas.edit', compact('disciplina', 'municipios'));
    }

    public function update(DisciplinaRequest $request, Disciplina $disciplina)
    {
        $disciplina->update($request->validated());
        return redirect()->route('admin.disciplinas.index')->with('success', 'Disciplina atualizada!');
    }

    public function destroy(Disciplina $disciplina)
    {
        if ($disciplina->turmas()->exists()) {
            return back()->with('error', 'Não é possível excluir uma disciplina vinculada a turmas.');
        }
        $disciplina->delete();
        return redirect()->route('admin.disciplinas.index')->with('success', 'Disciplina removida!');
    }
}
