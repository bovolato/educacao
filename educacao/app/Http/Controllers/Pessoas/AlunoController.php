<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\AlunoRequest;
use App\Models\Academico\{AnotacaoProfessor, Aula, Avaliacao, Frequencia, Nota};
use App\Models\Pessoas\{Aluno, Responsavel};
use App\Models\User;
use App\Models\Institucional\{Escola, Municipio};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlunoController extends Controller
{
    public function index(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $alunos = Aluno::query()
            ->with([
                'usuario:id,nome,cpf',
                'municipio:id,nome',
                'matriculas' => function ($q) {
                    $q->with([
                        'turma:id,nome',
                        'escola:id,nome',
                    ]);
                },
            ])
            ->when($escopo->escolaIdObrigatorioParaUsuarioEscola($user) !== null, fn ($q) => $escopo->aplicarEscopoAlunos($q, $user))
            ->when($request->filled('busca'), function ($q) use ($request) {
                $term = '%'.$request->busca.'%';
                $q->whereHas('usuario', function ($q) use ($term) {
                    $q->where('nome', 'like', $term)
                        ->orWhere('cpf', 'like', $term);
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'ativo') {
                    $q->where('ativo', true);
                } elseif ($request->status === 'inativo') {
                    $q->where('ativo', false);
                }
            })
            ->when($request->filled('municipio_id'), fn ($q) => $q->where('municipio_id', $request->municipio_id))
            ->when(! $request->filled('busca'), fn ($q) => $q->where('ativo', true))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $municipios = $this->municipiosDisponiveis($escopo, $user);

        return view('pessoas.alunos.index', compact('alunos', 'municipios'));
    }

    /** Municípios disponíveis ao usuário: o da escola fixa (gestor) ou todos os ativos. */
    private function municipiosDisponiveis(EscopoAcesso $escopo, User $user)
    {
        $municipioFixo = $escopo->escolaDoUsuario($user)?->municipio_id;

        return Municipio::query()
            ->where('ativo', true)
            ->when($municipioFixo, fn ($q) => $q->where('id', $municipioFixo))
            ->orderBy('nome')
            ->get(['id', 'nome', 'uf']);
    }

    public function create(Request $request)
    {
        $escopo = app(EscopoAcesso::class);
        $user   = $request->user();

        $municipios = $this->municipiosDisponiveis($escopo, $user);
        $municipioPadrao = $escopo->escolaDoUsuario($user)?->municipio_id;

        return view('pessoas.alunos.create', compact('municipios', 'municipioPadrao'));
    }

    public function store(AlunoRequest $request)
    {
        $escopo = app(EscopoAcesso::class);
        if ($esc = $escopo->escolaDoUsuario($request->user())) {
            $request->merge(['municipio_id' => $esc->municipio_id]);
        }

        DB::transaction(function () use ($request) {
            $usuario = User::create([
                'tipo'            => 'aluno',
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'nome_mae'        => $request->nome_mae,
                'nome_pai'        => $request->nome_pai,
                'naturalidade'    => $request->naturalidade,
                'naturalidade_uf' => $request->naturalidade_uf,
                'ativo'           => true,
            ]);

            if ($request->filled('telefone')) {
                $usuario->contatos()->create([
                    'tipo'      => 'celular',
                    'valor'     => $request->telefone,
                    'principal' => true,
                ]);
            }
            if ($request->filled('email_contato')) {
                $usuario->contatos()->create([
                    'tipo'      => 'email',
                    'valor'     => $request->email_contato,
                    'principal' => false,
                ]);
            }

            if ($request->filled('logradouro')) {
                $usuario->enderecos()->create([
                    'logradouro' => $request->logradouro,
                    'numero'     => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro'     => $request->bairro,
                    'cidade'     => $request->cidade,
                    'uf'         => $request->uf,
                    'cep'        => $request->cep,
                    'principal'  => true,
                ]);
            }

            $aluno = Aluno::create([
                'user_id'               => $usuario->id,
                'municipio_id'          => $request->municipio_id,
                'ra'                    => $request->ra,
                'codigo_aluno'          => $request->codigo_aluno,
                'nis'                   => $request->nis,
                'necessidades_especiais' => $request->boolean('necessidades_especiais'),
                'descricao_necessidades' => $request->descricao_necessidades,
                'usa_transporte'        => $request->boolean('usa_transporte'),
                'ativo'                 => true,
            ]);

            // Responsável (simples): nome + telefone
            if ($request->filled('responsavel_nome')) {
                $usuarioResp = User::create([
                    'tipo'  => 'responsavel',
                    'nome'  => $request->responsavel_nome,
                    'ativo' => true,
                ]);

                if ($request->filled('responsavel_telefone')) {
                    $usuarioResp->contatos()->create([
                        'tipo'      => 'celular',
                        'valor'     => $request->responsavel_telefone,
                        'principal' => true,
                    ]);
                }

                $resp = Responsavel::create([
                    'user_id' => $usuarioResp->id,
                ]);

                $aluno->responsaveis()->attach($resp->id, [
                    'responsavel_principal' => true,
                    'recebe_boletim'        => true,
                ]);
            }
        });

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function show(Request $request, Aluno $aluno)
    {
        $this->authorize('view', $aluno);

        $aluno->loadMissing([
            'usuario.contatos',
            'usuario.enderecos',
            'municipio',
            'responsaveis.usuario',
            'matriculas.turma.serie',
            'matriculas.turma.turno',
            'matriculas.escola',
        ]);

        $periodos = collect(['1B','2B','3B','4B']);
        $periodoSelecionado = $request->input('periodo', '1B');

        $matriculasAtivas = $aluno->matriculas
            ->where('situacao', 'ativa')
            ->sortByDesc('data_matricula')
            ->values();

        $matriculaSelecionada = null;
        if ($request->filled('matricula_id')) {
            $matriculaSelecionada = $aluno->matriculas->firstWhere('id', (int) $request->matricula_id);
        }
        if (! $matriculaSelecionada) {
            $matriculaSelecionada = $matriculasAtivas->first() ?? $aluno->matriculas->sortByDesc('data_matricula')->first();
        }

        $freqResumo = null;
        $avaliacoes = collect();
        $notasPorAvaliacao = collect();
        $anotacoesProfessor = collect();

        if ($matriculaSelecionada && $matriculaSelecionada->turma_id) {
            $freqResumo = Frequencia::query()
                ->join('aulas', 'frequencias.aula_id', '=', 'aulas.id')
                ->where('frequencias.matricula_id', $matriculaSelecionada->id)
                ->where('aulas.turma_id', $matriculaSelecionada->turma_id)
                ->where('aulas.periodo', $periodoSelecionado)
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='presente' THEN 1 ELSE 0 END) as presentes")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='falta' THEN 1 ELSE 0 END) as faltas")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='justificada' THEN 1 ELSE 0 END) as justificadas")
                ->selectRaw("SUM(CASE WHEN frequencias.situacao='atraso' THEN 1 ELSE 0 END) as atrasos")
                ->first();

            $avaliacoes = Avaliacao::query()
                ->where('turma_id', $matriculaSelecionada->turma_id)
                ->where('periodo', $periodoSelecionado)
                ->with(['disciplina', 'professor.usuario'])
                ->orderByDesc('data_avaliacao')
                ->orderByDesc('id')
                ->limit(50)
                ->get();

            $notasPorAvaliacao = Nota::query()
                ->whereIn('avaliacao_id', $avaliacoes->pluck('id'))
                ->where('matricula_id', $matriculaSelecionada->id)
                ->get()
                ->keyBy('avaliacao_id');

            $anotacoesProfessor = AnotacaoProfessor::query()
                ->where('matricula_id', $matriculaSelecionada->id)
                ->where('periodo', $periodoSelecionado)
                ->with('professor.usuario')
                ->orderByDesc('id')
                ->limit(30)
                ->get();
        }

        return view('pessoas.alunos.show', compact(
            'aluno',
            'periodos',
            'periodoSelecionado',
            'matriculasAtivas',
            'matriculaSelecionada',
            'freqResumo',
            'avaliacoes',
            'notasPorAvaliacao',
            'anotacoesProfessor',
        ));
    }

    public function edit(Aluno $aluno)
    {
        $this->authorize('update', $aluno);

        $aluno->loadMissing(['usuario.contatos', 'usuario.enderecos', 'responsaveis.usuario.contatos']);
        if (! $aluno->usuario) {
            return redirect()
                ->route('pessoas.alunos.index')
                ->with('error', 'Este aluno não possui cadastro de usuário vinculado. Corrija os dados no banco ou cadastre novamente.');
        }

        $escopo = app(EscopoAcesso::class);
        $user   = request()->user();

        $municipios = $this->municipiosDisponiveis($escopo, $user);
        $municipioPadrao = $escopo->escolaDoUsuario($user)?->municipio_id;

        return view('pessoas.alunos.edit', compact('aluno', 'municipios', 'municipioPadrao'));
    }

    public function update(AlunoRequest $request, Aluno $aluno)
    {
        $this->authorize('update', $aluno);

        $escopo = app(EscopoAcesso::class);
        if ($esc = $escopo->escolaDoUsuario($request->user())) {
            $request->merge(['municipio_id' => $esc->municipio_id]);
        }

        $aluno->loadMissing(['usuario', 'responsaveis.usuario.contatos']);
        if (! $aluno->usuario) {
            return redirect()
                ->route('pessoas.alunos.index')
                ->with('error', 'Não é possível atualizar: cadastro de usuário ausente para este aluno.');
        }

        DB::transaction(function () use ($request, $aluno) {
            $aluno->usuario->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo'            => $request->sexo,
                'nome_mae'        => $request->nome_mae,
                'nome_pai'        => $request->nome_pai,
                'naturalidade'    => $request->naturalidade,
                'naturalidade_uf' => $request->naturalidade_uf,
            ]);

            $aluno->update([
                'municipio_id'          => $request->municipio_id,
                'ra'                    => $request->ra,
                'codigo_aluno'          => $request->codigo_aluno,
                'nis'                   => $request->nis,
                'necessidades_especiais' => $request->boolean('necessidades_especiais'),
                'descricao_necessidades' => $request->descricao_necessidades,
                'usa_transporte'        => $request->boolean('usa_transporte'),
                'ativo'                 => $request->boolean('ativo', true),
            ]);

            if ($request->filled('telefone')) {
                $aluno->usuario->contatos()->updateOrCreate(
                    ['tipo' => 'celular'],
                    ['valor' => $request->telefone, 'principal' => true]
                );
            }
            if ($request->filled('email_contato')) {
                $aluno->usuario->contatos()->updateOrCreate(
                    ['tipo' => 'email'],
                    ['valor' => $request->email_contato]
                );
            }

            if ($request->filled('logradouro')) {
                $aluno->usuario->enderecos()->updateOrCreate(
                    ['principal' => true],
                    [
                        'logradouro' => $request->logradouro,
                        'numero'     => $request->numero,
                        'complemento' => $request->complemento,
                        'bairro'     => $request->bairro,
                        'cidade'     => $request->cidade,
                        'uf'         => $request->uf,
                        'cep'        => $request->cep,
                    ]
                );
            }

            // Responsável (simples): atualiza o principal; se não existir e vier nome, cria.
            if ($request->filled('responsavel_nome')) {
                $respPrincipal = $aluno->responsaveis()->wherePivot('responsavel_principal', true)->first()
                    ?? $aluno->responsaveis()->first();

                if (! $respPrincipal) {
                    $usuarioResp = User::create([
                        'tipo'  => 'responsavel',
                        'nome'  => $request->responsavel_nome,
                        'ativo' => true,
                    ]);
                    $respPrincipal = Responsavel::create(['user_id' => $usuarioResp->id]);
                    $aluno->responsaveis()->attach($respPrincipal->id, [
                        'responsavel_principal' => true,
                        'recebe_boletim'        => true,
                    ]);
                } else {
                    $respPrincipal->loadMissing('usuario.contatos');
                    $respPrincipal->usuario?->update(['nome' => $request->responsavel_nome]);
                }

                if ($request->filled('responsavel_telefone') && $respPrincipal?->usuario) {
                    $respPrincipal->usuario->contatos()->updateOrCreate(
                        ['tipo' => 'celular'],
                        ['valor' => $request->responsavel_telefone, 'principal' => true]
                    );
                }
            }
        });

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno atualizado com sucesso!');
    }

    public function destroy(Aluno $aluno)
    {
        $this->authorize('delete', $aluno);

        if ($aluno->matriculas()->where('situacao', 'ativa')->exists()) {
            return back()->with('error', 'Não é possível excluir um aluno com matrícula ativa.');
        }
        $aluno->update(['ativo' => false]);

        return redirect()->route('pessoas.alunos.index')->with('success', 'Aluno inativado.');
    }
}
