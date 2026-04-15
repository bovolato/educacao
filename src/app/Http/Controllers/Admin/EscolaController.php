<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EscolaRequest;
use App\Models\Institucional\{Escola, Municipio};
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    public function index(Request $request)
    {
        $query = Escola::with('municipio')
            ->withCount(['turmas', 'matriculas', 'salas']);

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->busca . '%')
                  ->orWhere('codigo', 'like', '%' . $request->busca . '%')
                  ->orWhere('inep', 'like', '%' . $request->busca . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $escolas = $query->orderBy('nome')->paginate(15)->withQueryString();

        return view('admin.escolas.index', compact('escolas'));
    }

    public function create()
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.escolas.create', compact('municipios'));
    }

    public function store(EscolaRequest $request)
    {
        Escola::create($request->validated());
        return redirect()->route('admin.escolas.index')
            ->with('success', 'Escola cadastrada com sucesso!');
    }

    public function show(Escola $escola)
    {
        $escola->load(['municipio', 'salas', 'professores.pessoa']);
        $turmas     = $escola->turmas()->with(['serie', 'turno'])->withCount('matriculasAtivas')->get();
        $matriculas = $escola->matriculas()->where('situacao', 'ativa')->count();

        return view('admin.escolas.show', compact('escola', 'turmas', 'matriculas'));
    }

    public function edit(Escola $escola)
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.escolas.edit', compact('escola', 'municipios'));
    }

    public function update(EscolaRequest $request, Escola $escola)
    {
        $escola->update($request->validated());
        return redirect()->route('admin.escolas.index')
            ->with('success', 'Escola atualizada com sucesso!');
    }

    public function destroy(Escola $escola)
    {
        if ($escola->turmas()->exists()) {
            return back()->with('error', 'Não é possível excluir uma escola com turmas cadastradas.');
        }
        $escola->delete();
        return redirect()->route('admin.escolas.index')
            ->with('success', 'Escola removida com sucesso!');
    }
}
