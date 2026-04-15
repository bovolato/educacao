<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SerieRequest;
use App\Models\Institucional\{Serie, EtapaEnsino};
use Illuminate\Http\Request;

class SerieController extends Controller
{
    public function index(Request $request)
    {
        $series = Serie::with('etapaEnsino')
            ->when($request->filled('busca'), fn($q) => $q->where('nome', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('etapa'), fn($q) => $q->where('etapa_ensino_id', $request->etapa))
            ->orderBy('etapa_ensino_id')->orderBy('ordem')
            ->paginate(20)->withQueryString();

        $etapas = EtapaEnsino::orderBy('nome')->get();

        return view('admin.series.index', compact('series', 'etapas'));
    }

    public function create()
    {
        $etapas = EtapaEnsino::orderBy('nome')->get();
        return view('admin.series.create', compact('etapas'));
    }

    public function store(SerieRequest $request)
    {
        Serie::create($request->validated());
        return redirect()->route('admin.series.index')->with('success', 'Série cadastrada!');
    }

    public function show(Serie $serie)
    {
        $serie->load(['etapaEnsino']);
        return view('admin.series.show', compact('serie'));
    }

    public function edit(Serie $serie)
    {
        $etapas = EtapaEnsino::orderBy('nome')->get();
        return view('admin.series.edit', compact('serie', 'etapas'));
    }

    public function update(SerieRequest $request, Serie $serie)
    {
        $serie->update($request->validated());
        return redirect()->route('admin.series.index')->with('success', 'Série atualizada!');
    }

    public function destroy(Serie $serie)
    {
        if ($serie->turmas()->exists()) {
            return back()->with('error', 'Não é possível excluir uma série com turmas vinculadas.');
        }
        $serie->delete();
        return redirect()->route('admin.series.index')->with('success', 'Série removida!');
    }
}
