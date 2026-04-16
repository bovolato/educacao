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

        if ($request->filled('cidade')) {
            $query->where('cidade', $request->cidade);
        }

        $escolas = $query->orderBy('nome')->paginate(10)->withQueryString();

        $cidades = Escola::query()
            ->whereNotNull('cidade')
            ->where('cidade', '!=', '')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        return view('admin.escolas.index', compact('escolas', 'cidades'));
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
        $escola->load(['municipio', 'salas']);

        $turmas = $escola->turmas()
            ->with(['serie', 'turno'])
            ->withCount('matriculasAtivas')
            ->orderBy('nome')
            ->paginate(10, ['*'], 'turmas_page')
            ->withQueryString();

        $professores = $escola->professores()
            ->with('pessoa')
            ->where('ativo', true)
            ->orderBy('id')
            ->paginate(10, ['*'], 'professores_page')
            ->withQueryString();

        $matriculas = $escola->matriculas()->where('situacao', 'ativa')->count();

        return view('admin.escolas.show', compact('escola', 'turmas', 'professores', 'matriculas'));
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
