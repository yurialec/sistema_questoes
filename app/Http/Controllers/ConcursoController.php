<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alternativa;
use App\Models\HistoricoResposta;
use App\Models\Questao;
use Illuminate\Http\Request;

class ConcursoController extends Controller
{
    public function responder($id)
    {
        $questao = Questao::with('alternativas')
            ->findOrFail($id);

        return view(
            'questoes.responder',
            compact('questao')
        );
    }

    public function verificar(Request $request)
    {
        $alternativa = Alternativa::findOrFail(
            $request->alternativa_id
        );

        HistoricoResposta::create([
            'questao_id' => $alternativa->questao_id,
            'alternativa_id' => $alternativa->id,
            'acertou' => $alternativa->correta,
            'respondido_em' => now()
        ]);

        return back()->with(
            'resultado',
            $alternativa->correta
        );
    }
}
