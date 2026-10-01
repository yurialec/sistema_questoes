<?php

namespace App\Http\Controllers;

use App\Models\HistoricoResposta;
use App\Models\Materia;
use App\Models\ProgressoQuestao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function curvaAprendizagem(Request $request)
    {
        $userId = Auth::id();
        $materiaId = $request->input('materia_id');
        $assuntoId = $request->input('assunto_id');

        // ============================================
        // 1. BASE: Busca todo o progresso do usuário
        // ============================================
        $queryProgresso = ProgressoQuestao::where('user_id', $userId)
            ->with(['questao.assunto.materia', 'questao.materia']);

        if ($materiaId) {
            $queryProgresso->whereHas('questao', fn($q) => $q->where('materia_id', $materiaId));
        }
        if ($assuntoId) {
            $queryProgresso->whereHas('questao', fn($q) => $q->where('assunto_id', $assuntoId));
        }

        $progressos = $queryProgresso->get();

        // ============================================
        // 2. KPIs (Cards do topo)
        // ============================================
        $totalCartoes = $progressos->count();
        
        // Domínio médio (% nas caixas 4 e 5)
        $dominadas = $progressos->whereIn('caixa_leitner', [4, 5])->count();
        $dominioMedio = $totalCartoes > 0 ? round(($dominadas / $totalCartoes) * 100, 1) : 0;

        // Revisões hoje (proxima_revisao <= hoje)
        $revisoesHoje = $progressos->filter(function($p) {
            return $p->proxima_revisao && $p->proxima_revisao->isToday();
        })->count();

        // Sequência de dias (dias consecutivos estudando)
        $diasEstudados = HistoricoResposta::where('user_id', $userId)
            ->where('respondido_em', '>=', now()->subDays(60))
            ->select(DB::raw('DATE(respondido_em) as data'))
            ->distinct()
            ->orderBy('data', 'DESC')
            ->pluck('data')
            ->map(fn($d) => Carbon::parse($d)->startOfDay());

        $sequencia = 0;
        $hoje = now()->startOfDay();
        foreach (range(0, 365) as $diasAtras) {
            $dataAlvo = $hoje->copy()->subDays($diasAtras);
            if ($diasEstudados->contains($dataAlvo)) {
                $sequencia++;
            } else {
                break;
            }
        }

        // ============================================
        // 3. DADOS DO GRÁFICO DE LINHA (Evolução)
        // ============================================
        $queryHistorico = HistoricoResposta::where('user_id', $userId)
            ->where('respondido_em', '>=', now()->subDays(30));

        if ($materiaId || $assuntoId) {
            $queryHistorico->whereHas('questao', function($q) use ($materiaId, $assuntoId) {
                if ($materiaId) $q->where('materia_id', $materiaId);
                if ($assuntoId) $q->where('assunto_id', $assuntoId);
            });
        }

        $historico = $queryHistorico->select(
                DB::raw('DATE(respondido_em) as data'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN acertou = 1 THEN 1 ELSE 0 END) as acertos')
            )
            ->groupBy('data')
            ->orderBy('data', 'ASC')
            ->get();

        $labelsLinha = [];
        $dadosLinha = [];
        foreach ($historico as $item) {
            $labelsLinha[] = Carbon::parse($item->data)->format('d/m');
            $dadosLinha[] = $item->total > 0 ? round(($item->acertos / $item->total) * 100, 1) : 0;
        }

        // ============================================
        // 4. DADOS DO GRÁFICO DE ROSCA (Ciclo Leitner)
        // ============================================
        $totalCaixas = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($progressos as $p) {
            $totalCaixas[$p->caixa_leitner]++;
        }
        $labelsRosca = ['Caixa 1 (1 dia)', 'Caixa 2 (3 dias)', 'Caixa 3 (7 dias)', 'Caixa 4 (15 dias)', 'Caixa 5 (30 dias)'];
        $dadosRosca = array_values($totalCaixas);

        // ============================================
        // 5. TABELA DE DESEMPENHO POR ASSUNTO
        // ============================================
        $progressosAgrupados = $progressos->groupBy('questao.assunto_id');
        $dadosAssuntos = [];

        foreach ($progressosAgrupados as $assuntoId => $items) {
            $assunto = $items->first()->questao->assunto;
            if (!$assunto) continue;

            $caixas = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            foreach ($items as $item) {
                $caixas[$item->caixa_leitner]++;
            }

            $total = $items->count();
            $dominadasAssunto = $caixas[4] + $caixas[5];
            $porcentagemDominio = $total > 0 ? round(($dominadasAssunto / $total) * 100, 1) : 0;

            // Próxima revisão (a mais próxima entre as questões do assunto)
            $proximaRevisao = $items->filter(fn($i) => $i->proxima_revisao)
                ->sortBy('proxima_revisao')
                ->first()?->proxima_revisao;

            // Status
            if ($porcentagemDominio >= 80) $status = 'Dominado';
            elseif ($porcentagemDominio >= 50) $status = 'Em progresso';
            elseif ($porcentagemDominio >= 20) $status = 'Aprendizado';
            else $status = 'Iniciante';

            $dadosAssuntos[] = [
                'assunto_id' => $assunto->id,
                'nome' => $assunto->nome,
                'total' => $total,
                'caixas' => $caixas,
                'porcentagem_dominio' => $porcentagemDominio,
                'proxima_revisao' => $proximaRevisao,
                'status' => $status,
            ];
        }

        usort($dadosAssuntos, fn($a, $b) => $b['porcentagem_dominio'] <=> $a['porcentagem_dominio']);

        // ============================================
        // 6. DADOS PARA OS FILTROS (Matérias e Assuntos)
        // ============================================
        $materiasComProgresso = Materia::whereHas('questoes.progresso', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $assuntosComProgresso = collect();
        if ($materiaId) {
            $assuntosComProgresso = \App\Models\Assunto::where('materia_id', $materiaId)
                ->whereHas('questoes.progresso', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('nome')
                ->get(['id', 'nome']);
        }

        $materiaSelecionada = $materiasComProgresso->firstWhere('id', $materiaId);
        $assuntoSelecionado = $assuntosComProgresso->firstWhere('id', $assuntoId);

        return view('relatorios.curva_aprendizagem', compact(
            'totalCartoes',
            'dominioMedio',
            'revisoesHoje',
            'sequencia',
            'labelsLinha',
            'dadosLinha',
            'labelsRosca',
            'dadosRosca',
            'dadosAssuntos',
            'materiasComProgresso',
            'assuntosComProgresso',
            'materiaSelecionada',
            'assuntoSelecionado'
        ));
    }
}