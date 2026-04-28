<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Academico\{DocumentoEmitido, Matricula};
use App\Services\EscopoAcesso;
use Illuminate\Http\Request;

class DocumentoEscolaController extends Controller
{
    private function escolaIdObrigatorio(): int
    {
        $eid = app(EscopoAcesso::class)->escolaIdObrigatorioParaUsuarioEscola(auth()->user());
        abort_if($eid === null, 403);

        return $eid;
    }

    public function index()
    {
        $eid = $this->escolaIdObrigatorio();

        $documentos = DocumentoEmitido::query()
            ->with(['aluno.pessoa', 'matricula.turma', 'emitidoPor'])
            ->where(function ($q) use ($eid) {
                $q->whereHas('matricula', fn ($m) => $m->where('escola_id', $eid))
                    ->orWhereHas('aluno.matriculas', fn ($m) => $m->where('escola_id', $eid));
            })
            ->orderByDesc('emitido_em')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('escola.documentos.index', compact('documentos'));
    }

    public function create()
    {
        $eid = $this->escolaIdObrigatorio();

        $matriculas = Matricula::query()
            ->where('escola_id', $eid)
            ->where('situacao', 'ativa')
            ->with(['aluno.pessoa', 'turma'])
            ->join('alunos', 'matriculas.aluno_id', '=', 'alunos.id')
            ->join('pessoas', 'alunos.pessoa_id', '=', 'pessoas.id')
            ->orderBy('pessoas.nome')
            ->select('matriculas.*')
            ->get();

        $tipos = [
            'declaracao_matricula' => 'Declaração de matrícula',
            'atestado_frequencia'  => 'Atestado de frequência (referência)',
        ];

        return view('escola.documentos.create', compact('matriculas', 'tipos'));
    }

    public function store(Request $request)
    {
        $eid = $this->escolaIdObrigatorio();

        $data = $request->validate([
            'matricula_id'   => 'required|exists:matriculas,id',
            'tipo_documento' => 'required|string|max:80',
        ]);

        $matricula = Matricula::findOrFail($data['matricula_id']);
        abort_if((int) $matricula->escola_id !== $eid, 403);

        $doc = DocumentoEmitido::create([
            'aluno_id'               => $matricula->aluno_id,
            'matricula_id'           => $matricula->id,
            'tipo_documento'         => $data['tipo_documento'],
            'emitido_por_usuario_id' => auth()->id(),
            'emitido_em'             => now(),
        ]);

        return redirect()
            ->route('escola.documentos.imprimir', $doc)
            ->with('success', 'Documento registrado. Use a impressão do navegador se desejar PDF.');
    }

    public function imprimir(DocumentoEmitido $documentoEmitido)
    {
        $eid = $this->escolaIdObrigatorio();

        $documentoEmitido->load(['aluno.pessoa', 'matricula.turma.serie', 'matricula.escola', 'matricula.anoLetivo', 'emitidoPor']);

        if ($documentoEmitido->matricula && (int) $documentoEmitido->matricula->escola_id !== $eid) {
            abort(403);
        }

        return view('escola.documentos.imprimir', ['doc' => $documentoEmitido]);
    }
}
