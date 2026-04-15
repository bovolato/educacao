<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\ProfessorRequest;
use App\Models\Pessoas\{Professor, Pessoa, PessoaContato};
use App\Models\Institucional\Escola;
use App\Models\Academico\{Turma, Disciplina};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $professores = Professor::query()
            ->with([
                'pessoa:id,nome',
                'escola:id,nome',
            ])
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('pessoa', fn ($q) => $q->where('nome', 'like', $term));
            })
            ->when($request->filled('escola'), fn ($q) => $q->where('escola_id', $request->escola))
            ->when($request->filled('ativo'), fn ($q) => $q->where('ativo', $request->ativo === '1'))
            ->when(! $request->filled('busca') && ! $request->filled('ativo'), fn ($q) => $q->where('ativo', true))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $escolas = Escola::query()
            ->where('status', 'ativa')
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('pessoas.professores.index', compact('professores', 'escolas'));
    }

    public function create()
    {
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        return view('pessoas.professores.create', compact('escolas'));
    }

    public function store(ProfessorRequest $request)
    {
        DB::transaction(function () use ($request) {
            $pessoa = Pessoa::create([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'ativo'           => true,
            ]);

            if ($request->filled('telefone')) {
                PessoaContato::create(['pessoa_id' => $pessoa->id, 'tipo' => 'celular', 'valor' => $request->telefone, 'principal' => true]);
            }
            if ($request->filled('email_contato')) {
                PessoaContato::create(['pessoa_id' => $pessoa->id, 'tipo' => 'email', 'valor' => $request->email_contato, 'principal' => false]);
            }

            Professor::create([
                'pessoa_id'             => $pessoa->id,
                'escola_id'             => $request->escola_id,
                'matricula_funcional'   => $request->matricula_funcional,
                'formacao'              => $request->formacao,
                'registro_profissional' => $request->registro_profissional,
                'data_admissao'         => $request->data_admissao,
                'ativo'                 => true,
            ]);
        });

        return redirect()->route('pessoas.professores.index')->with('success', 'Professor cadastrado com sucesso!');
    }

    public function show(Professor $professor)
    {
        $professor->load(['pessoa.contatos', 'escola', 'turmas.serie', 'turmas.turno']);
        return view('pessoas.professores.show', compact('professor'));
    }

    public function edit(Professor $professor)
    {
        $professor->load(['pessoa.contatos']);
        $escolas = Escola::where('status', 'ativa')->orderBy('nome')->get();
        return view('pessoas.professores.edit', compact('professor', 'escolas'));
    }

    public function update(ProfessorRequest $request, Professor $professor)
    {
        DB::transaction(function () use ($request, $professor) {
            $professor->pessoa->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
            ]);

            $professor->update([
                'escola_id'             => $request->escola_id,
                'matricula_funcional'   => $request->matricula_funcional,
                'formacao'              => $request->formacao,
                'registro_profissional' => $request->registro_profissional,
                'data_admissao'         => $request->data_admissao,
                'ativo'                 => $request->boolean('ativo', true),
            ]);

            if ($request->filled('telefone')) {
                $professor->pessoa->contatos()->updateOrCreate(['tipo' => 'celular'], ['valor' => $request->telefone, 'principal' => true]);
            }
            if ($request->filled('email_contato')) {
                $professor->pessoa->contatos()->updateOrCreate(['tipo' => 'email'], ['valor' => $request->email_contato]);
            }
        });

        return redirect()->route('pessoas.professores.index')->with('success', 'Professor atualizado!');
    }

    public function destroy(Professor $professor)
    {
        $professor->update(['ativo' => false]);
        return redirect()->route('pessoas.professores.index')->with('success', 'Professor inativado.');
    }

    public function vincularTurmas(Professor $professor)
    {
        $professor->load(['pessoa', 'escola', 'turmas']);

        $turmas = Turma::where('escola_id', $professor->escola_id)
            ->where('status', 'ativa')
            ->with(['serie', 'turno'])
            ->orderBy('nome')
            ->get();

        $disciplinas = Disciplina::where('ativo', true)->orderBy('nome')->get();

        $vinculosAtuais = DB::table('turma_professores')
            ->where('professor_id', $professor->id)
            ->get()
            ->map(fn($v) => $v->turma_id . '-' . $v->disciplina_id)
            ->toArray();

        return view('pessoas.professores.vincular-turmas', compact('professor', 'turmas', 'disciplinas', 'vinculosAtuais'));
    }

    public function salvarVinculoTurmas(Request $request, Professor $professor)
    {
        $request->validate([
            'vinculos'               => 'nullable|array',
            'vinculos.*.turma_id'    => 'required|exists:turmas,id',
            'vinculos.*.disciplina_id' => 'required|exists:disciplinas,id',
            'vinculos.*.titular'     => 'boolean',
        ]);

        DB::table('turma_professores')->where('professor_id', $professor->id)->delete();

        if ($request->filled('vinculos')) {
            foreach ($request->vinculos as $vinculo) {
                DB::table('turma_professores')->insert([
                    'turma_id'      => $vinculo['turma_id'],
                    'disciplina_id' => $vinculo['disciplina_id'],
                    'professor_id'  => $professor->id,
                    'titular'       => $vinculo['titular'] ?? true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        return redirect()->route('pessoas.professores.show', $professor)
            ->with('success', 'Vínculos de turmas atualizados com sucesso!');
    }
}
