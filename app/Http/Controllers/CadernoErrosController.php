<?php

namespace App\Http\Controllers;

use App\Models\CadernoErro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CadernoErrosController extends Controller
{
    public function index(Request $request)
    {
        $dataFiltro = $request->input('data');

        if ($dataFiltro) {
            $erros = CadernoErro::with([
                'questao.materia',
                'questao.assunto',
                'questao.cargo.orgao',
                'questao.ano',
                'questao.banca',
                'questao.textoComplementar',
                'questao.alternativas',
                'alternativa'
            ])
                ->where('user_id', Auth::id())
                ->whereDate('created_at', $dataFiltro)
                ->orderByDesc('created_at')
                ->get();

            $modoFiltro = true;
        } else {
            $primeiroPendente = CadernoErro::where('user_id', Auth::id())
                ->where('status', 'pendente')
                ->orderBy('created_at')
                ->value('created_at');

            if ($primeiroPendente) {
                $dataFiltro = Carbon::parse($primeiroPendente)->toDateString();

                $erros = CadernoErro::with([
                    'questao.materia',
                    'questao.assunto',
                    'questao.cargo.orgao',
                    'questao.ano',
                    'questao.banca',
                    'questao.textoComplementar',
                    'questao.alternativas',
                    'alternativa'
                ])
                    ->where('user_id', Auth::id())
                    ->where('status', 'pendente')
                    ->whereDate('created_at', $dataFiltro)
                    ->orderByDesc('created_at')
                    ->get();
            } else {
                $erros = collect();
                $dataFiltro = now()->toDateString();
            }

            $modoFiltro = false;
        }

        return view('caderno_erros.index', compact('erros', 'dataFiltro', 'modoFiltro'));
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
