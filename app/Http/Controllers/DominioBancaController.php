<?php

namespace App\Http\Controllers;

use App\Models\Banca;
use App\Models\DominioBanca;
use App\Models\HistoricoResposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DominioBancaController extends Controller
{
    public function store(Request $request)
    {
        $dados = $request->validate([
            'bancas' => ['required', 'array', 'min:1'],
            'bancas.*' => ['required', 'integer', 'distinct', 'exists:bancas,id'],
        ]);

        $userId = Auth::id();
        $bancas = Banca::query()
            ->withCount('questoes')
            ->whereIn('id', $dados['bancas'])
            ->get();

        $estatisticas = HistoricoResposta::query()
            ->join('questoes', 'questoes.id', '=', 'historico_respostas.questao_id')
            ->where('historico_respostas.user_id', $userId)
            ->whereIn('questoes.banca_id', $bancas->pluck('id'))
            ->select('questoes.banca_id')
            ->selectRaw('SUM(CASE WHEN historico_respostas.acertou = 1 THEN 1 ELSE 0 END) AS qtd_acertos')
            ->selectRaw('SUM(CASE WHEN historico_respostas.acertou = 0 THEN 1 ELSE 0 END) AS qtd_erros')
            ->groupBy('questoes.banca_id')
            ->get()
            ->keyBy('banca_id');

        $novasBancas = 0;

        DB::transaction(function () use ($bancas, $estatisticas, $userId, &$novasBancas) {
            foreach ($bancas as $banca) {
                $stats = $estatisticas->get($banca->id);
                $dominio = DominioBanca::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'banca_id' => $banca->id,
                    ],
                    [
                        'qtd_acertos' => (int) ($stats->qtd_acertos ?? 0),
                        'qtd_erros' => (int) ($stats->qtd_erros ?? 0),
                        'total_questoes_banca' => $banca->questoes_count,
                    ]
                );

                if ($dominio->wasRecentlyCreated) {
                    $novasBancas++;
                }
            }
        });

        $mensagem = $novasBancas > 0
            ? 'Bancas adicionadas ao acompanhamento.'
            : 'As bancas selecionadas já estavam sendo acompanhadas.';

        return redirect()->route('dashboard')->with('success', $mensagem);
    }
}
