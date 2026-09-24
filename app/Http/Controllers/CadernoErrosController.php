<?php

namespace App\Http\Controllers;

use App\Models\CadernoErro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CadernoErrosController extends Controller
{
    public function index(Request $request)
    {
        $dataFiltro = $request->input('data', now()->toDateString());

        $erros = CadernoErro::with(['questao.materia', 'alternativa'])
            ->where('user_id', Auth::id())
            ->whereDate('created_at', $dataFiltro)
            ->orderByDesc('created_at')
            ->get();

        return view('caderno_erros.index', compact('erros', 'dataFiltro'));
    }

    public function updateComoResolver(Request $request, CadernoErro $erro)
    {
        if ($erro->user_id !== Auth::id()) abort(403);

        $erro->update([
            'como_resolver' => $request->input('como_resolver')
        ]);

        return back()->with('success', 'Anotação de resolução salva com sucesso!');
    }
}
