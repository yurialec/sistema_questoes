<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alternativa;
use App\Models\Ano;
use App\Models\Banca;
use App\Models\CadernoErro;
use App\Models\Cargo;
use App\Models\FiltroSalvo;
use App\Models\HistoricoResposta;
use App\Models\Materia;
use App\Models\Orgao;
use App\Models\Questao;
use App\Services\LeitnerService;
use App\Services\MetaAprovacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConcursoController extends Controller
{
    public function responder(Request $request)
    {
        $user = Auth::user();
        $queryParams = $request->query();
        $userId = $user->id;

        if (empty($queryParams)) {
            $filtroPadrao = FiltroSalvo::where('user_id', $userId)
                ->where('is_padrao', true)
                ->first();

            if ($filtroPadrao) {
                $request->merge($filtroPadrao->filtros);
            }
        }

        $query = Questao::with([
            'cargo.orgao',
            'cargo.banca',
            'cargo.ano',
            'materia',
            'assunto',
            'alternativas'
        ])
            ->select('questoes.*')

            ->selectSub(function ($q) use ($userId) {
                $q->selectRaw('COUNT(hr.id)')
                    ->from('historico_respostas as hr')
                    ->join('questoes as q_sub', 'hr.questao_id', '=', 'q_sub.id')
                    ->whereColumn('q_sub.assunto_id', 'questoes.assunto_id')
                    ->where('hr.user_id', $userId);
            }, 'total_respostas_assunto')

            ->selectSub(function ($q) use ($userId) {
                $q->selectRaw('COALESCE(SUM(CASE WHEN hr.acertou = 0 THEN 1 ELSE 0 END), 0)')
                    ->from('historico_respostas as hr')
                    ->join('questoes as q_sub', 'hr.questao_id', '=', 'q_sub.id')
                    ->whereColumn('q_sub.assunto_id', 'questoes.assunto_id')
                    ->where('hr.user_id', $userId);
            }, 'total_erros_assunto')

            ->leftJoin('progresso_questoes as pq', function ($join) use ($userId) {
                $join->on('questoes.id', '=', 'pq.questao_id')
                    ->where('pq.user_id', '=', $userId);
            })
            ->addSelect('pq.proxima_revisao');

        if ($request->filled('orgao_id') && is_array($request->orgao_id) && !empty($request->orgao_id)) {
            $query->whereHas('cargo', function ($q) use ($request) {
                $q->whereIn('orgao_id', $request->orgao_id);
            });
        }

        if ($request->filled('banca_id') && is_array($request->banca_id) && !empty($request->banca_id)) {
            $query->whereHas('cargo', function ($q) use ($request) {
                $q->whereIn('banca_id', $request->banca_id);
            });
        }

        if ($request->filled('ano_id') && is_array($request->ano_id) && !empty($request->ano_id)) {
            $query->whereHas('cargo', function ($q) use ($request) {
                $q->whereIn('ano_id', $request->ano_id);
            });
        }

        if ($request->filled('cargo_id') && is_array($request->cargo_id) && !empty($request->cargo_id)) {
            $query->whereIn('cargo_id', $request->cargo_id);
        }

        if ($request->filled('materia_id') && is_array($request->materia_id) && !empty($request->materia_id)) {
            $query->whereIn('materia_id', $request->materia_id);
        }


        $query->orderBy('total_respostas_assunto', 'ASC')
            ->orderByRaw('CASE WHEN total_respostas_assunto = 0 THEN 0 ELSE (total_erros_assunto * 1.0 / total_respostas_assunto) END DESC')
            ->orderByRaw('CASE WHEN pq.proxima_revisao <= ? THEN 0 ELSE 1 END ASC', [now()])
            ->orderBy('pq.proxima_revisao', 'ASC')
            ->inRandomOrder();

        $questoes = $query->paginate(10);

        return view('questoes.responder', [
            'questoes' => $questoes,
            'orgaos' => Orgao::orderBy('nome')->get(),
            'bancas' => Banca::orderBy('nome')->get(),
            'anos' => Ano::orderBy('ano', 'desc')->get(),
            'cargos' => Cargo::orderBy('nome')->get(),
            'materias' => Materia::orderBy('nome')->get(),
            'filtrosSalvos' => FiltroSalvo::where('user_id', $userId)->orderBy('nome')->get(),
        ]);
    }

    public function verificar(Request $request, LeitnerService $leitnerService, MetaAprovacaoService $metaAprovacaoService)
    {
        $alternativa = Alternativa::with('questao')->findOrFail($request->alternativa_id);
        $user = Auth::user();

        HistoricoResposta::create([
            'user_id' => $user->id,
            'questao_id' => $alternativa->questao_id,
            'alternativa_id' => $alternativa->id,
            'acertou' => $alternativa->correta,
            'respondido_em' => now()
        ]);

        $metaAprovacaoService->recalcularProgresso($user->id);

        $leitnerService->processarResposta($user, $alternativa->questao, $alternativa->correta);

        if (!$alternativa->correta && $user->ativo_modal_erros) {
            $erro = CadernoErro::create([
                'user_id' => $user->id,
                'questao_id' => $alternativa->questao_id,
                'alternativa_id' => $alternativa->id,
                'status' => 'pendente'
            ]);

            return redirect()->back()->with([
                'resultado' => false,
                'show_error_modal' => true,
                'erro_id' => $erro->id,
                'questao_foco_id' => $alternativa->questao_id
            ]);
        }

        return back()->with('resultado', $alternativa->correta);
    }

    public function salvarMotivoErro(Request $request, CadernoErro $erro)
    {
        if ($erro->user_id !== Auth::id()) abort(403);

        $erro->update([
            'foi_chute' => $request->has('foi_chute'),
            'erro_distraido' => $request->has('erro_distraido'),
            'motivo_erro' => $request->input('motivo_erro')
        ]);

        return redirect()->route('responder')->with('success', 'Erro registrado no Caderno com sucesso!');
    }

    public function salvarFiltro(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nome_filtro' => 'required|string|max:255',
            'definir_padrao' => 'nullable|boolean',
        ]);

        $filtrosData = $request->only(['orgao_id', 'banca_id', 'ano_id', 'cargo_id', 'materia_id']);

        $filtrosData = array_filter($filtrosData, function ($item) {
            return !empty($item);
        });

        if ($request->has('definir_padrao')) {
            FiltroSalvo::where('user_id', $user->id)->update(['is_padrao' => false]);
        }

        FiltroSalvo::create([
            'user_id' => $user->id,
            'nome' => $request->nome_filtro,
            'filtros' => $filtrosData,
            'is_padrao' => $request->boolean('definir_padrao', false),
        ]);

        return redirect()->back()->with('success', 'Filtro salvo com sucesso!');
    }

    public function excluirFiltro($id)
    {
        $filtro = FiltroSalvo::where('user_id', Auth::id())->findOrFail($id);
        $filtro->delete();

        return redirect()->back()->with('success', 'Filtro excluído com sucesso!');
    }
}
