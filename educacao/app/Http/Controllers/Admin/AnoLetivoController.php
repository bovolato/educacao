<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnoLetivoRequest;
use App\Models\Institucional\{AnoLetivo, Municipio};
use Illuminate\Http\Request;

class AnoLetivoController extends Controller
{
    public function index(Request $request)
    {
        $anos = AnoLetivo::with('municipio')
            ->when($request->filled('busca'), fn($q) => $q->where('descricao', 'like', '%' . $request->busca . '%'))
            ->orderByDesc('descricao')
            ->paginate(10)
            ->withQueryString();

        return view('admin.anos-letivos.index', compact('anos'));
    }

    public function create()
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.anos-letivos.create', compact('municipios'));
    }

    public function store(AnoLetivoRequest $request)
    {
        if ($request->ativo) {
            AnoLetivo::where('municipio_id', $request->municipio_id)->update(['ativo' => false]);
        }
        AnoLetivo::create($request->validated());
        return redirect()->route('admin.anos-letivos.index')->with('success', 'Ano letivo cadastrado!');
    }

    public function show(AnoLetivo $anoLetivo)
    {
        $anoLetivo->load('municipio');
        return view('admin.anos-letivos.show', compact('anoLetivo'));
    }

    public function edit(AnoLetivo $anoLetivo)
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.anos-letivos.edit', compact('anoLetivo', 'municipios'));
    }

    public function update(AnoLetivoRequest $request, AnoLetivo $anoLetivo)
    {
        if ($request->ativo) {
            AnoLetivo::where('municipio_id', $anoLetivo->municipio_id)
                ->where('id', '!=', $anoLetivo->id)
                ->update(['ativo' => false]);
        }
        $anoLetivo->update($request->validated());
        return redirect()->route('admin.anos-letivos.index')->with('success', 'Ano letivo atualizado!');
    }

    public function destroy(AnoLetivo $anoLetivo)
    {
        if ($anoLetivo->turmas()->exists()) {
            return back()->with('error', 'Não é possível excluir um ano letivo com turmas vinculadas.');
        }
        $anoLetivo->delete();
        return redirect()->route('admin.anos-letivos.index')->with('success', 'Ano letivo removido!');
    }
}
