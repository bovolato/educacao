<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MunicipioRequest;
use App\Models\Institucional\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index(Request $request)
    {
        $query = Municipio::withCount('escolas');

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->busca . '%')
                  ->orWhere('codigo_ibge', 'like', '%' . $request->busca . '%');
            });
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        $municipios = $query->orderBy('nome')->paginate(15)->withQueryString();

        return view('admin.municipios.index', compact('municipios'));
    }

    public function create()
    {
        return view('admin.municipios.create');
    }

    public function store(MunicipioRequest $request)
    {
        Municipio::create($request->validated());
        return redirect()->route('admin.municipios.index')
            ->with('success', 'Município cadastrado com sucesso!');
    }

    public function show(Municipio $municipio)
    {
        $municipio->loadCount('escolas');
        $escolas = $municipio->escolas()->withCount('turmas')->orderBy('nome')->get();

        return view('admin.municipios.show', compact('municipio', 'escolas'));
    }

    public function edit(Municipio $municipio)
    {
        return view('admin.municipios.edit', compact('municipio'));
    }

    public function update(MunicipioRequest $request, Municipio $municipio)
    {
        $municipio->update($request->validated());
        return redirect()->route('admin.municipios.index')
            ->with('success', 'Município atualizado com sucesso!');
    }

    public function destroy(Municipio $municipio)
    {
        if ($municipio->escolas()->exists()) {
            return back()->with('error', 'Não é possível excluir um município com escolas cadastradas.');
        }
        $municipio->delete();
        return redirect()->route('admin.municipios.index')
            ->with('success', 'Município removido com sucesso!');
    }
}
