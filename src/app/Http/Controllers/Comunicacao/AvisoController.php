<?php

namespace App\Http\Controllers\Comunicacao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comunicacao\AvisoRequest;
use App\Models\Comunicacao\Aviso;
use App\Models\Institucional\Escola;
use App\Models\Academico\Turma;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    public function index(Request $request)
    {
        $avisos = Aviso::with(['municipio', 'escola', 'usuario'])
            ->when($request->filled('busca'), fn($q) => $q->where('titulo', 'like', '%' . $request->busca . '%'))
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo_destino', $request->tipo))
            ->orderByDesc('publicado_em')
            ->paginate(15)->withQueryString();

        return view('avisos.index', compact('avisos'));
    }

    public function create()
    {
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $turmas  = Turma::where('status', 'ativa')->with('serie')->orderBy('nome')->get();
        return view('avisos.create', compact('escolas', 'turmas'));
    }

    public function store(AvisoRequest $request)
    {
        $aviso = Aviso::create(array_merge($request->validated(), [
            'municipio_id' => auth()->user()->municipio_id,
            'usuario_id'   => auth()->id(),
            'publicado_em' => now(),
        ]));

        return redirect()->route('avisos.index')->with('success', 'Aviso publicado com sucesso!');
    }

    public function show(Aviso $aviso)
    {
        $aviso->load(['municipio', 'escola', 'usuario', 'destinatarios']);
        return view('avisos.show', compact('aviso'));
    }

    public function edit(Aviso $aviso)
    {
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        $turmas  = Turma::where('status', 'ativa')->with('serie')->orderBy('nome')->get();
        return view('avisos.edit', compact('aviso', 'escolas', 'turmas'));
    }

    public function update(AvisoRequest $request, Aviso $aviso)
    {
        $aviso->update($request->validated());
        return redirect()->route('avisos.index')->with('success', 'Aviso atualizado!');
    }

    public function destroy(Aviso $aviso)
    {
        $aviso->update(['ativo' => false]);
        return redirect()->route('avisos.index')->with('success', 'Aviso desativado.');
    }
}
