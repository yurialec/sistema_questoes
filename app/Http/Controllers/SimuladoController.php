<?php

namespace App\Http\Controllers;

use App\Models\Ano;
use App\Models\Banca;
use App\Models\Cargo;
use App\Models\Orgao;
use App\Models\Questao;
use App\Models\Simulado;
use App\Models\SimuladoQuestao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class SimuladoController extends Controller
{
    public function index(Request $request)
    {
        $query = Simulado::query()
            ->where('user_id', Auth::id())
            ->withCount([
                'questoes as respondidas_count' => function ($q) {
                    $q->whereNotNull('alternativa_id');
                },
            ])
            ->latest();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->input('nome') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->input('data_fim'));
        }

        $simulados = $query->paginate(15)->withQueryString();

        $orgaos = Orgao::orderBy('nome')->get();
        $bancas = Banca::orderBy('nome')->get();
        $anos   = Ano::orderByDesc('ano')->get();
        $cargos = Cargo::orderBy('nome')->get();

        return view('simulados.index', compact(
            'simulados',
            'orgaos',
            'bancas',
            'anos',
            'cargos',
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'orgao_id' => ['required', 'exists:orgaos,id'],
            'cargo_id' => ['required', 'exists:cargos,id'],
            'banca_id' => ['required', 'exists:bancas,id'],
            'ano_id'   => ['required', 'exists:anos,id'],
            'nome'     => ['nullable', 'string', 'max:255'],
        ]);

        // Busca todas as questões da prova (órgão + cargo + banca + ano),
        // já trazendo as alternativas para garantir consistência.
        $questoes = Questao::query()
            ->where('cargo_id', $data['cargo_id'])
            ->where('banca_id', $data['banca_id'])
            ->where('ano_id', $data['ano_id'])
            ->whereHas('cargo', function ($q) use ($data) {
                $q->where('orgao_id', $data['orgao_id']);
            })
            ->with(['materia', 'alternativas'])
            ->get();

        if ($questoes->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Nenhuma questão encontrada para essa prova.');
        }

        // Agrupa por matéria e ordena: matérias em ordem alfabética,
        // dentro da matéria mantém a ordem original da consulta.
        $questoesAgrupadas = $questoes
            ->sortBy(function ($q) {
                return mb_strtolower($q->materia->nome ?? '');
            })
            ->groupBy('materia_id');

        // Gera um nome padrão se o usuário não informar
        $nome = $data['nome'] ?? sprintf(
            'Simulado #%s - %s/%s',
            now()->format('d/m/Y H:i'),
            $questoes->first()->banca->nome ?? 'Banca',
            $questoes->first()->ano->ano ?? '—'
        );

        $simulado = DB::transaction(function () use ($data, $questoesAgrupadas, $nome) {
            $simulado = Simulado::create([
                'user_id'                => Auth::id(),
                'nome'                   => $nome,
                'filtros'                => [
                    'orgao_id' => $data['orgao_id'],
                    'cargo_id' => $data['cargo_id'],
                    'banca_id' => $data['banca_id'],
                    'ano_id'   => $data['ano_id'],
                ],
                'status'                 => 'nao_iniciado',
                'total_questoes'         => $questoesAgrupadas->flatten()->count(),
            ]);

            $ordem = 1;
            foreach ($questoesAgrupadas as $materiaId => $questoesDaMateria) {
                foreach ($questoesDaMateria as $questao) {
                    SimuladoQuestao::create([
                        'simulado_id' => $simulado->id,
                        'questao_id'  => $questao->id,
                        'materia_id'  => $materiaId,
                        'ordem'       => $ordem++,
                    ]);
                }
            }

            return $simulado;
        });

        return redirect()
            ->route('simulados.show', $simulado)
            ->with('success', 'Simulado criado com sucesso!');
    }

    public function destroy(Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);

        $simulado->delete();

        return redirect()
            ->route('simulados.index')
            ->with('success', 'Simulado excluído com sucesso.');
    }

    public function show(Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);

        $simulado->load([
            'questoes' => function ($q) {
                $q->orderBy('ordem');
            },
            'questoes.questao.alternativas' => function ($q) {
                $q->orderBy('letra');
            },
            'questoes.questao.materia',
            'questoes.questao.textoComplementar',
            'questoes.questao.assunto',
            'questoes.questao.banca',
            'questoes.questao.ano',
            'questoes.questao.cargo.orgao',
        ]);

        $questoesPorMateria = $simulado->questoes
            ->groupBy(fn($sq) => $sq->materia_id)
            ->map(function ($grupo) {
                return [
                    'materia' => $grupo->first()->questao->materia ?? null,
                    'questoes' => $grupo->values(),
                ];
            })
            ->values();

        return view('simulados.show', compact('simulado', 'questoesPorMateria'));
    }

    public function iniciar(Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);
        abort_if($simulado->status !== 'nao_iniciado', 400);

        $simulado->update([
            'status' => 'em_andamento',
            'iniciado_em' => now(),
        ]);

        return redirect()->route('simulados.show', $simulado);
    }

    public function pausar(Request $request, Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);
        abort_if(!in_array($simulado->status, ['em_andamento', 'pausado']), 400);

        $this->salvarRespostas($request, $simulado);

        $simulado->update(['status' => 'pausado']);

        return redirect()
            ->route('simulados.index')
            ->with('success', 'Simulado pausado. Você pode retomá-lo quando quiser.');
    }

    public function retomar(Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);
        abort_if($simulado->status !== 'pausado', 400);

        $simulado->update(['status' => 'em_andamento']);

        return redirect()->route('simulados.show', $simulado);
    }

    public function finalizar(Request $request, Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);
        abort_if($simulado->status !== 'em_andamento', 400);

        $this->salvarRespostas($request, $simulado);

        $simulado->refresh()->load('questoes');

        $semResposta = $simulado->questoes->whereNull('alternativa_id')->count();

        if ($semResposta > 0) {
            return back()->with('error', "Ainda faltam {$semResposta} questão(ões) para responder.");
        }

        $acertos = 0;
        $erros = 0;

        foreach ($simulado->questoes as $sq) {
            $correta = $sq->questao->alternativas
                ->firstWhere('id', $sq->alternativa_id)?->correta ?? false;

            $sq->update(['correta' => $correta]);

            $correta ? $acertos++ : $erros++;
        }

        $simulado->update([
            'status' => 'finalizado',
            'total_acertos' => $acertos,
            'total_erros' => $erros,
            'finalizado_em' => now(),
        ]);

        return redirect()->route('simulados.show', $simulado);
    }

    protected function salvarRespostas(Request $request, Simulado $simulado): void
    {
        $respostas = $request->input('respostas', []);

        if (empty($respostas)) {
            return;
        }

        foreach ($respostas as $questaoId => $alternativaId) {
            if (!$alternativaId) {
                continue;
            }

            SimuladoQuestao::where('simulado_id', $simulado->id)
                ->where('questao_id', $questaoId)
                ->update([
                    'alternativa_id' => $alternativaId,
                    'respondida_em' => now(),
                ]);
        }
    }

    public function estatisticas()
    {
        $simulados = Simulado::query()
            ->where('user_id', Auth::id())
            ->where('status', 'finalizado')
            ->with(['questoes.materia'])
            ->orderBy('finalizado_em')
            ->get();

        $totalSimulados   = $simulados->count();
        $totalQuestoes    = $simulados->sum('total_questoes');
        $totalAcertos     = $simulados->sum('total_acertos');
        $totalErros       = $simulados->sum('total_erros');
        $aproveitamentoMedio = $totalQuestoes > 0
            ? round($totalAcertos / $totalQuestoes * 100, 1)
            : 0;

        $tempoTotalSegundos = $simulados->sum(fn($s) => $s->tempo_total_segundos ?? 0);
        $tempoMedioSegundos = $totalSimulados > 0
            ? (int) round($tempoTotalSegundos / $totalSimulados)
            : 0;

        $evolucao = $simulados->map(function ($s) {
            $perc = $s->total_questoes > 0
                ? round(($s->total_acertos ?? 0) / $s->total_questoes * 100, 1)
                : 0;

            return [
                'data'   => optional($s->finalizado_em)->format('d/m/Y') ?? $s->created_at->format('d/m/Y'),
                'nome'   => $s->nome,
                'perc'   => $perc,
                'acertos' => $s->total_acertos,
                'erros'  => $s->total_erros,
                'total'  => $s->total_questoes,
            ];
        })->values();

        $porMateria = [];
        foreach ($simulados as $s) {
            foreach ($s->questoes as $sq) {
                $materiaId  = $sq->materia_id;
                $nome       = $sq->materia->nome ?? 'Sem matéria';

                if (!isset($porMateria[$materiaId])) {
                    $porMateria[$materiaId] = [
                        'nome'    => $nome,
                        'acertos' => 0,
                        'erros'   => 0,
                    ];
                }

                $sq->correta
                    ? $porMateria[$materiaId]['acertos']++
                    : $porMateria[$materiaId]['erros']++;
            }
        }

        usort($porMateria, fn($a, $b) => strcmp($a['nome'], $b['nome']));

        return view('simulados.estatisticas', [
            'totalSimulados'      => $totalSimulados,
            'totalQuestoes'       => $totalQuestoes,
            'totalAcertos'        => $totalAcertos,
            'totalErros'          => $totalErros,
            'aproveitamentoMedio' => $aproveitamentoMedio,
            'tempoTotalSegundos'  => $tempoTotalSegundos,
            'tempoMedioSegundos'  => $tempoMedioSegundos,
            'evolucao'            => $evolucao,
            'porMateria'          => $porMateria,
        ]);
    }

    public function exportarJson(Simulado $simulado)
    {
        abort_if($simulado->user_id !== Auth::id(), 403);

        $simulado->load([
            'questoes' => fn($q) => $q->orderBy('ordem'),
            'questoes.questao.alternativas' => fn($q) => $q->orderBy('letra'),
            'questoes.questao.materia',
            'questoes.questao.assunto',
            'questoes.questao.banca',
            'questoes.questao.ano',
            'questoes.questao.cargo.orgao',
            'questoes.questao.textoComplementar',
        ]);

        $payload = [
            'simulado' => [
                'id'                    => $simulado->id,
                'nome'                  => $simulado->nome,
                'status'                => $simulado->status,
                'total_questoes'        => $simulado->total_questoes,
                'total_acertos'         => $simulado->total_acertos,
                'total_erros'           => $simulado->total_erros,
                'tempo_total_segundos'  => $simulado->tempo_total_segundos,
                'criado_em'             => optional($simulado->created_at)->toIso8601String(),
                'iniciado_em'           => optional($simulado->iniciado_em)->toIso8601String(),
                'finalizado_em'         => optional($simulado->finalizado_em)->toIso8601String(),
            ],
            'questoes' => $simulado->questoes->map(function ($sq) {
                $q = $sq->questao;

                return [
                    'ordem'            => $sq->ordem,
                    'correta'          => (bool) $sq->correta,
                    'respondida_em'    => optional($sq->respondida_em)->toIso8601String(),
                    'alternativa_id'   => $sq->alternativa_id,

                    'questao' => [
                        'id'             => $q->id,
                        'materia'        => optional($q->materia)->nome,
                        'assunto'        => optional($q->assunto)->nome,
                        'banca'          => optional($q->banca)->nome,
                        'ano'            => optional($q->ano)->ano,
                        'orgao'          => optional(optional($q->cargo)->orgao)->nome,
                        'cargo'          => optional($q->cargo)->nome,
                        'enunciado'      => $q->enunciado,
                        'tabela_html'    => $q->tabela_html,
                        'imagem'         => $q->imagem,
                        'texto_complementar' => optional($q->textoComplementar)->conteudo,
                        'alternativas'   => $q->alternativas->map(fn($alt) => [
                            'id'        => $alt->id,
                            'letra'     => $alt->letra,
                            'descricao' => $alt->descricao,
                            'correta'   => (bool) $alt->correta,
                        ])->values(),
                    ],
                ];
            })->values(),
        ];

        $nomeArquivo = 'simulado-' . $simulado->id . '-' . Str::slug($simulado->nome) . '.json';

        return Response::make(
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            200,
            [
                'Content-Type'        => 'application/json; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
            ]
        );
    }
}
