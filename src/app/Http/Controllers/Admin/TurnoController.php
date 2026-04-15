<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TurnoRequest;
use App\Models\Institucional\{Turno, Municipio};
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index(Request $request)
    {
        $turnos = Turno::with('municipio')
            ->when($request->filled('busca'), fn($q) => $q->where('nome', 'like', '%' . $request->busca . '%'))
            ->orderBy('nome')->paginate(20)->withQueryString();

        return view('admin.turnos.index', compact('turnos'));
    }

    public function create()
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.turnos.create', compact('municipios'));
    }

    public function store(TurnoRequest $request)
    {
        Turno::create($request->validated());
        return redirect()->route('admin.turnos.index')->with('success', 'Turno cadastrado!');
    }

    public function show(Turno $turno)
    {
        return view('admin.turnos.show', compact('turno'));
    }

    public function edit(Turno $turno)
    {
        $municipios = Municipio::where('ativo', true)->orderBy('nome')->get();
        return view('admin.turnos.edit', compact('turno', 'municipios'));
    }

    public function update(TurnoRequest $request, Turno $turno)
    {
        $turno->update($request->validated());
        return redirect()->route('admin.turnos.index')->with('success', 'Turno atualizado!');
    }

    public function destroy(Turno $turno)
    {
        if ($turno->turmas()->exists()) {
            return back()->with('error', 'Não é possível excluir um turno vinculado a turmas.');
        }
        $turno->delete();
        return redirect()->route('admin.turnos.index')->with('success', 'Turno removido!');
    }
}
