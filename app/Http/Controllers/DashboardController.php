<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HistoricoResposta;
use App\Models\Materia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Totais gerais ---
        $total    = HistoricoResposta::count();
        $acertos  = HistoricoResposta::where('acertou', true)->count();
        $erros    = HistoricoResposta::where('acertou', false)->count();
        $percentual = $total > 0 ? round(($acertos / $total) * 100) : 0;

        // --- Sequência de dias (streak) ---
        $diasRespondidos = HistoricoResposta::selectRaw('DATE(respondido_em) as dia')
            ->distinct()
            ->orderByDesc('dia')
            ->pluck('dia')
            ->toArray();

        $streak = 0;
        if (!empty($diasRespondidos)) {
            $hoje   = Carbon::today()->toDateString();
            $ontem  = Carbon::yesterday()->toDateString();

            // Streak só conta se a última resposta foi hoje ou ontem
            if ($diasRespondidos[0] === $hoje || $diasRespondidos[0] === $ontem) {
                $esperado = $diasRespondidos[0];
                foreach ($diasRespondidos as $dia) {
                    if ($dia === $esperado) {
                        $streak++;
                        $esperado = Carbon::parse($dia)->subDay()->toDateString();
                    } else {
                        break;
                    }
                }
            }
        }

        // --- Dias desde a última resposta ---
        $ultimaRespondidaEm = HistoricoResposta::latest('respondido_em')->value('respondido_em');
        $diasSemResponder   = null;
        $ultimaRespostaLabel = 'Nunca';

        if ($ultimaRespondidaEm) {
            $ultima = Carbon::parse($ultimaRespondidaEm);
            $diasSemResponder = (int) $ultima->diffInDays(now());

            $ultimaRespostaLabel = match (true) {
                $diasSemResponder === 0 => 'Hoje',
                $diasSemResponder === 1 => 'Ontem',
                default                 => "Há {$diasSemResponder} dias",
            };
        }

        // --- Nível do estudante baseado em total de questões ---
        $nivel = match (true) {
            $total === 0          => ['titulo' => 'Novato',     'proximo' => 'Iniciante',   'meta' => 10],
            $total < 10           => ['titulo' => 'Iniciante',  'proximo' => 'Estudioso',   'meta' => 50],
            $total < 50           => ['titulo' => 'Estudioso',  'proximo' => 'Dedicado',    'meta' => 200],
            $total < 200          => ['titulo' => 'Dedicado',   'proximo' => 'Avançado',    'meta' => 500],
            $total < 500          => ['titulo' => 'Avançado',   'proximo' => 'Expert',      'meta' => 1000],
            default               => ['titulo' => 'Expert',     'proximo' => null,          'meta' => null],
        };

        // --- Matérias com progresso ---
        // Total de questões por matéria
        $materias = Materia::withCount('questoes as total_questoes')->get();

        // Questões únicas respondidas e acertos por matéria (via JOIN)
        $estatisticasPorMateria = DB::table('historico_respostas as hr')
            ->join('questoes as q', 'q.id', '=', 'hr.questao_id')
            ->select(
                'q.materia_id',
                DB::raw('COUNT(hr.id) as total_tentativas'),
                DB::raw('COUNT(DISTINCT hr.questao_id) as questoes_respondidas'),
                DB::raw('SUM(hr.acertou) as total_acertos')
            )
            ->groupBy('q.materia_id')
            ->get()
            ->keyBy('materia_id');

        $materias->each(function ($materia) use ($estatisticasPorMateria) {
            $stats = $estatisticasPorMateria->get($materia->id);

            $materia->questoes_respondidas = $stats?->questoes_respondidas ?? 0;
            $materia->total_tentativas     = $stats?->total_tentativas ?? 0;
            $materia->total_acertos        = $stats?->total_acertos ?? 0;
            $materia->percentual_progresso = $materia->total_questoes > 0
                ? round(($materia->questoes_respondidas / $materia->total_questoes) * 100)
                : 0;
            $materia->percentual_acertos   = $materia->total_tentativas > 0
                ? round(($materia->total_acertos / $materia->total_tentativas) * 100)
                : 0;
        });

        return view('dashboard.index', compact(
            'total',
            'acertos',
            'erros',
            'percentual',
            'streak',
            'diasSemResponder',
            'ultimaRespostaLabel',
            'nivel',
            'materias',
        ));
    }

    public function resetar()
    {
        HistoricoResposta::truncate();

        return redirect()
            ->back()
            ->with(
                'success',
                'Estatísticas zeradas.'
            );
    }
}
