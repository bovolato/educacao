<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TurmasProfessorController extends Controller
{
    public function index()
    {
        $prof = auth()->user()->professor;

        $vinculos = DB::table('turma_professores as tp')
            ->where('tp.professor_id', $prof->id)
            ->join('turmas as t', 't.id', '=', 'tp.turma_id')
            ->join('disciplinas as d', 'd.id', '=', 'tp.disciplina_id')
            ->leftJoin('escolas as e', 'e.id', '=', 't.escola_id')
            ->orderBy('t.nome')
            ->orderBy('d.nome')
            ->select([
                't.id as turma_id', 't.nome as turma_nome', 't.status as turma_status',
                't.polivalente as turma_polivalente',
                'd.id as disciplina_id', 'd.nome as disciplina_nome',
                'e.nome as escola_nome',
            ])
            ->get();

        $turmasAgrupadas = $vinculos->groupBy('turma_id');

        return view('professor.turmas.index', compact('vinculos', 'turmasAgrupadas'));
    }
}
