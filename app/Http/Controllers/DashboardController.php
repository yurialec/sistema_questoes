<?php

namespace App\Http\Controllers;

use App\Models\CadernoErro;
use App\Models\GradeEstudo;
use App\Models\HistoricoResposta;
use App\Models\Materia;
use App\Models\MetaAprovacao;
use App\Models\ProgressoQuestao;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $estatisticas = $this->obterEstatisticasGerais();

        return view('dashboard.index', [
            ...$estatisticas,
            'streak' => $this->calcularSequenciaDeEstudos(),
            ...$this->obterUltimaAtividade(),
            'nivel' => $this->obterNivel($estatisticas['total']),
            'materias' => $this->obterMateriasComEstatisticas(),
            ...$this->obterDadosGrafico(),
            'sugestaoHoje' => $this->getSugestaoDeHoje(),
            'metaAtiva' => $this->obterMetaAtiva(),
        ]);
    }

    private function obterEstatisticasGerais(): array
    {
        $total = HistoricoResposta::count();
        $acertos = HistoricoResposta::where('acertou', true)->count();
        $erros = HistoricoResposta::where('acertou', false)->count();

        return [
            'total' => $total,
            'acertos' => $acertos,
            'erros' => $erros,
            'percentual' => $total > 0 ? round(($acertos / $total) * 100) : 0,
        ];
    }

    private function calcularSequenciaDeEstudos(): int
    {
        $diasRespondidos = HistoricoResposta::selectRaw('DATE(respondido_em) as dia')
            ->distinct()
            ->orderByDesc('dia')
            ->pluck('dia')
            ->toArray();

        if (empty($diasRespondidos)) {
            return 0;
        }

        $hoje = Carbon::today()->toDateString();
        $ontem = Carbon::yesterday()->toDateString();

        if ($diasRespondidos[0] !== $hoje && $diasRespondidos[0] !== $ontem) {
            return 0;
        }

        $streak = 0;
        $esperado = $diasRespondidos[0];

        foreach ($diasRespondidos as $dia) {
            if ($dia !== $esperado) {
                break;
            }

            $streak++;
            $esperado = Carbon::parse($dia)->subDay()->toDateString();
        }

        return $streak;
    }

    private function obterUltimaAtividade(): array
    {
        $ultimaRespondidaEm = HistoricoResposta::latest('respondido_em')
            ->value('respondido_em');

        if (!$ultimaRespondidaEm) {
            return [
                'diasSemResponder' => null,
                'ultimaRespostaLabel' => 'Nunca',
            ];
        }

        $ultima = Carbon::parse($ultimaRespondidaEm);
        $diasSemResponder = (int) $ultima->diffInDays(now());

        $ultimaRespostaLabel = match (true) {
            $diasSemResponder === 0 => 'Hoje',
            $diasSemResponder === 1 => 'Ontem',
            default => "Há {$diasSemResponder} dias",
        };

        return [
            'diasSemResponder' => $diasSemResponder,
            'ultimaRespostaLabel' => $ultimaRespostaLabel,
        ];
    }

    private function obterNivel(int $total): array
    {
        return match (true) {
            $total === 0 => ['titulo' => 'Novato', 'proximo' => 'Iniciante', 'meta' => 10],
            $total < 10 => ['titulo' => 'Iniciante', 'proximo' => 'Estudioso', 'meta' => 50],
            $total < 50 => ['titulo' => 'Estudioso', 'proximo' => 'Dedicado', 'meta' => 200],
            $total < 200 => ['titulo' => 'Dedicado', 'proximo' => 'Avançado', 'meta' => 500],
            $total < 500 => ['titulo' => 'Avançado', 'proximo' => 'Expert', 'meta' => 1000],
            default => ['titulo' => 'Expert', 'proximo' => null, 'meta' => null],
        };
    }

    private function obterMateriasComEstatisticas()
    {
        $materias = Materia::withCount('questoes as total_questoes')->get();

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
            $materia->total_tentativas = $stats?->total_tentativas ?? 0;
            $materia->total_acertos = $stats?->total_acertos ?? 0;
            $materia->percentual_progresso = $materia->total_questoes > 0
                ? round(($materia->questoes_respondidas / $materia->total_questoes) * 100)
                : 0;
            $materia->percentual_acertos = $materia->total_tentativas > 0
                ? round(($materia->total_acertos / $materia->total_tentativas) * 100)
                : 0;
        });

        return $materias;
    }

    private function obterDadosGrafico(): array
    {
        $graficoDados = DB::table('historico_respostas as hr')
            ->select(
                DB::raw('DATE(hr.respondido_em) as data'),
                DB::raw('SUM(CASE WHEN hr.acertou = 1 THEN 1 ELSE 0 END) as acertos'),
                DB::raw('SUM(CASE WHEN hr.acertou = 0 THEN 1 ELSE 0 END) as erros'),
                DB::raw('COUNT(*) as total')
            )
            ->where('hr.respondido_em', '>=', now()->subDays(14))
            ->groupBy('data')
            ->orderBy('data', 'asc')
            ->get();

        $datas = [];
        $acertosData = [];
        $errosData = [];

        for ($i = 13; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('Y-m-d');
            $datas[] = $data;

            $registro = $graficoDados->firstWhere('data', $data);

            if ($registro) {
                $acertosData[] = (int) $registro->acertos;
                $errosData[] = (int) $registro->erros;
            } else {
                $acertosData[] = 0;
                $errosData[] = 0;
            }
        }

        $datasFormatadas = array_map(function ($data) {
            return Carbon::parse($data)->format('d/m');
        }, $datas);

        return [
            'datasFormatadas' => $datasFormatadas,
            'acertosData' => $acertosData,
            'errosData' => $errosData,
        ];
    }

    private function obterMetaAtiva(): ?MetaAprovacao
    {
        return MetaAprovacao::with('cargo')
            ->where('user_id', Auth::id())
            ->first();
    }

    public function desempenhoPorMateria()
    {
        $materiasData = Materia::with('assuntos')->get()->map(function ($materia) {
            $materiaTotalRespondidas = 0;
            $materiaTotalAcertos = 0;
            $materiaTotalErros = 0;

            $assuntosData = $materia->assuntos->map(function ($assunto) use (&$materiaTotalRespondidas, &$materiaTotalAcertos, &$materiaTotalErros) {
                $stats = DB::table('historico_respostas as hr')
                    ->join('questoes as q', 'q.id', '=', 'hr.questao_id')
                    ->where('q.assunto_id', $assunto->id)
                    ->select(
                        DB::raw('COUNT(hr.id) as respondidas'),
                        DB::raw('SUM(CASE WHEN hr.acertou = 1 THEN 1 ELSE 0 END) as acertos'),
                        DB::raw('SUM(CASE WHEN hr.acertou = 0 THEN 1 ELSE 0 END) as erros')
                    )
                    ->first();

                $respondidas = $stats->respondidas ?? 0;
                $acertos = $stats->acertos ?? 0;
                $erros = $stats->erros ?? 0;
                $totalQuestoes = $assunto->questoes()->count();

                $aproveitamento = $respondidas > 0 ? round(($acertos / $respondidas) * 100) : 0;

                $materiaTotalRespondidas += $respondidas;
                $materiaTotalAcertos += $acertos;
                $materiaTotalErros += $erros;

                return [
                    'nome' => $assunto->nome,
                    'total_questoes' => $totalQuestoes,
                    'respondidas' => $respondidas,
                    'acertos' => $acertos,
                    'erros' => $erros,
                    'aproveitamento' => $aproveitamento,
                ];
            });

            $materiaAproveitamento = $materiaTotalRespondidas > 0 ? round(($materiaTotalAcertos / $materiaTotalRespondidas) * 100) : 0;

            return [
                'id' => $materia->id,
                'nome' => $materia->nome,
                'tipo' => $materia->tipo,
                'total_respondidas' => $materiaTotalRespondidas,
                'total_acertos' => $materiaTotalAcertos,
                'total_erros' => $materiaTotalErros,
                'aproveitamento' => $materiaAproveitamento,
                'assuntos' => $assuntosData->filter(fn($a) => $a['respondidas'] > 0)->values(),
            ];
        });

        // Filtra apenas matérias que possuem pelo menos uma questão respondida
        $materiasData = $materiasData->filter(fn($m) => $m['total_respondidas'] > 0)->values();

        return view('dashboard.desempenho', compact('materiasData'));
    }

    public function resetar()
    {
        $userId = Auth::id();

        HistoricoResposta::where('user_id', $userId)->delete();
        CadernoErro::where('user_id', $userId)->delete();
        ProgressoQuestao::where('user_id', $userId)->delete();

        MetaAprovacao::where('user_id', $userId)->update([
            'porcentagem' => 0,
            'rank'        => 'ruim'
        ]);

        return redirect()->back()->with('success', 'Suas estatísticas, Caderno de Erros, Curva de Aprendizagem e o progresso da sua Meta foram zerados com sucesso.');
    }

    public function getSugestaoDeHoje()
    {
        $userId = Auth::id();

        if (!$userId) {
            return null;
        }

        $meta = MetaAprovacao::where('user_id', $userId)->first();

        if (!$meta) {
            return $this->sugestaoInteligente($userId);
        }

        $materiasIds = DB::table('questoes')
            ->where('cargo_id', $meta->cargo_id)
            ->whereNotNull('materia_id')
            ->distinct()
            ->pluck('materia_id')
            ->all();

        if (empty($materiasIds)) {
            return null;
        }

        return $this->sugestaoInteligente($userId, $materiasIds);
    }

    private function sugestaoInteligente(
        int $userId,
        ?array $materiasIds = null
    ) {
        $hoje = now('America/Sao_Paulo');

        // Normaliza os IDs para manter o cache consistente.
        if ($materiasIds !== null) {
            $materiasIds = array_values(
                array_unique(array_map('intval', $materiasIds))
            );

            sort($materiasIds);

            if (empty($materiasIds)) {
                return null;
            }
        }

        // Se a meta mudar, o cache também será diferente.
        $escopo = $materiasIds === null
            ? 'todas'
            : 'meta_' . md5(implode(',', $materiasIds));

        $cacheKey = "sugestao_estudo_{$userId}_"
            . $hoje->format('Y-m-d')
            . "_{$escopo}";

        $materiaId = Cache::remember(
            $cacheKey,
            $hoje->copy()->endOfDay(),
            function () use ($userId, $materiasIds) {

                // Busca somente as matérias permitidas.
                $query = Materia::query();

                if ($materiasIds !== null) {
                    $query->whereIn('id', $materiasIds);
                }

                $todasMaterias = $query->get();

                if ($todasMaterias->isEmpty()) {
                    return null;
                }

                $idsPermitidos = $todasMaterias
                    ->pluck('id')
                    ->all();

                // REGRA 1:
                // Identifica as matérias que o usuário já respondeu.
                $materiasRespondidasIds = HistoricoResposta::query()
                    ->join(
                        'questoes',
                        'historico_respostas.questao_id',
                        '=',
                        'questoes.id'
                    )
                    ->where('historico_respostas.user_id', $userId)
                    ->whereIn('questoes.materia_id', $idsPermitidos)
                    ->distinct()
                    ->pluck('questoes.materia_id')
                    ->all();

                // REGRA 2:
                // Prioriza matérias que ainda não foram respondidas.
                $materiasNaoRespondidas = $todasMaterias
                    ->whereNotIn('id', $materiasRespondidasIds);

                if ($materiasNaoRespondidas->isNotEmpty()) {
                    return $materiasNaoRespondidas->random()->id;
                }

                // REGRA 3:
                // Todas as matérias já foram respondidas.
                // Identifica a matéria com menor aproveitamento.
                $stats = DB::table('historico_respostas as hr')
                    ->join(
                        'questoes as q',
                        'hr.questao_id',
                        '=',
                        'q.id'
                    )
                    ->where('hr.user_id', $userId)
                    ->whereIn('q.materia_id', $idsPermitidos)
                    ->select(
                        'q.materia_id',
                        DB::raw(
                            'SUM(CASE WHEN hr.acertou = 1 THEN 1 ELSE 0 END) as total_acertos'
                        ),
                        DB::raw('COUNT(*) as total_tentativas')
                    )
                    ->groupBy('q.materia_id')
                    ->get();

                $materiaPiorDesempenhoId = null;
                $menorTaxa = 101;

                foreach ($stats as $stat) {
                    if ($stat->total_tentativas <= 0) {
                        continue;
                    }

                    $taxa = (
                        $stat->total_acertos /
                        $stat->total_tentativas
                    ) * 100;

                    if ($taxa < $menorTaxa) {
                        $menorTaxa = $taxa;
                        $materiaPiorDesempenhoId = $stat->materia_id;
                    }
                }

                return $materiaPiorDesempenhoId
                    ?? $todasMaterias->random()->id;
            }
        );

        return $materiaId
            ? Materia::find($materiaId)
            : null;
    }
}
