<?php

namespace App\Http\Controllers;

use App\Models\Alternativa;
use App\Models\CadernoErro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReaplicacaoController extends Controller
{
    public function index()
    {
        $totalPendentes = CadernoErro::where('user_id', Auth::id())
            ->where('status', 'pendente')
            ->count();

        return view('reaplicacao.index', compact('totalPendentes'));
    }

    public function iniciar()
    {
        $erro = CadernoErro::with([
            'questao.alternativas',
            'questao.textoComplementar',
            'alternativa' // Para saber qual foi a errada anteriormente, se precisar
        ])
            ->where('user_id', Auth::id())
            ->where('status', 'pendente')
            ->inRandomOrder()
            ->first();

        if (!$erro) {
            return view('reaplicacao.vazio'); // View de parabéns
        }

        return view('reaplicacao.responder', [
            'erro' => $erro,
            'mostrar_anotacoes' => false // Começa "a frio"
        ]);
    }

    public function verificar(Request $request, CadernoErro $erro)
    {
        if ($erro->user_id !== Auth::id()) abort(403);

        $alternativaEscolhida = Alternativa::findOrFail($request->alternativa_id);
        $acertou = $alternativaEscolhida->correta;

        if ($acertou) {
            // Superou o erro!
            $erro->update(['status' => 'superado']);
            return redirect()->route('reaplicacao.iniciar')
                ->with('resultado', true)
                ->with('success', 'Parabéns! Você superou este erro.');
        } else {
            // Errou de novo: Mostra as anotações antigas para reforço
            return view('reaplicacao.responder', [
                'erro' => $erro,
                'mostrar_anotacoes' => true, // Flag para mostrar o box de revisão
                'resultado' => false,
                'alternativa_escolhida_id' => $alternativaEscolhida->id
            ]);
        }
    }
}
