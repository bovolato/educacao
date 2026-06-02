<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContextoProfessorController extends Controller
{
    public function setPeriodo(Request $request)
    {
        $data = $request->validate([
            'periodo' => 'required|in:1B,2B,3B,4B',
            'return_to' => 'nullable|string|max:2048',
        ]);

        $request->session()->put('professor_periodo', $data['periodo']);

        $to = $data['return_to'] ?? url()->previous();
        if (! $to) {
            $to = route('professor.turmas.index');
        }

        return redirect($to);
    }
}

