<?php

namespace App\Http\Controllers;

use App\Models\HistoricoResposta;
use App\Models\ProgressoQuestao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function curvaAprendizagem()
    {
        $userId = Auth::id();

        // ============================================
        // 1. DADOS DA TABELA (já existia)
        // ============================================
        $progressos = ProgressoQuestao::where('user_id', $userId)
            ->with('questao.assunto')
            ->get()
            ->groupBy('questao.assunto_id');

        $dadosAssuntos = [];
        $totalCaixas = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($progressos as $assuntoId => $items) {
            $assuntoNome = $items->first()->questao->assunto->nome ?? 'Sem Assunto';
            $caixas = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

            foreach ($items as $item) {
                $caixas[$item->caixa_leitner]++;
                $totalCaixas[$item->caixa_leitner]++;
            }

            $total = $items->count();
            $dominadas = $caixas[4] + $caixas[5];
            $porcentagemDominio = $total > 0 ? round(($dominadas / $total) * 100, 1) : 0;

            $dadosAssuntos[] = [
                'assunto_id' => $assuntoId,
                'nome' => $assuntoNome,
                'total' => $total,
                'caixas' => $caixas,
                'porcentagem_dominio' => $porcentagemDominio,
            ];
        }

        usort($dadosAssuntos, function ($a, $b) {
            return $a['porcentagem_dominio'] <=> $b['porcentagem_dominio'];
        });

        // ============================================
        // 2. DADOS DO GRÁFICO DE LINHA (Evolução)
        // ============================================
        // Agrupa respostas dos últimos 30 dias por data
        $historico = HistoricoResposta::where('user_id', $userId)
            ->where('respondido_em', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(respondido_em) as data'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN acertou = 1 THEN 1 ELSE 0 END) as acertos')
            )
            ->groupBy('data')
            ->orderBy('data', 'ASC')
            ->get();

        // Monta arrays para o Chart.js
        $labelsLinha = [];
        $dadosLinha = [];

        foreach ($historico as $item) {
            $labelsLinha[] = Carbon::parse($item->data)->format('d/m');
            $percentual = $item->total > 0 ? round(($item->acertos / $item->total) * 100, 1) : 0;
            $dadosLinha[] = $percentual;
        }

        // ============================================
        // 3. DADOS DO GRÁFICO DE BARRAS (Top 5 piores assuntos)
        // ============================================
        $top5Piores = array_slice($dadosAssuntos, 0, 5); // Já está ordenado do pior para o melhor
        $labelsBarras = array_column($top5Piores, 'nome');
        $dadosBarras = array_column($top5Piores, 'porcentagem_dominio');

        // ============================================
        // 4. DADOS DO GRÁFICO DE ROSCA (Distribuição das caixas)
        // ============================================
        $labelsRosca = ['Cx 1 (Revisão)', 'Cx 2', 'Cx 3', 'Cx 4', 'Cx 5 (Mestre)'];
        $dadosRosca = array_values($totalCaixas);

        return view('relatorios.curva_aprendizagem', compact(
            'dadosAssuntos',
            'labelsLinha',
            'dadosLinha',
            'labelsBarras',
            'dadosBarras',
            'labelsRosca',
            'dadosRosca'
        ));
    }
}
