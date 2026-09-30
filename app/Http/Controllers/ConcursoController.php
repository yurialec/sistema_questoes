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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConcursoController extends Controller
{
    public function responder(Request $request)
    {
        $user = Auth::user();
        $queryParams = $request->query();

        // 1. Lógica Inteligente: Se não houver filtros na URL, carrega o filtro padrão do usuário
        if (empty($queryParams)) {
            $filtroPadrao = FiltroSalvo::where('user_id', $user->id)
                ->where('is_padrao', true)
                ->first();

            if ($filtroPadrao) {
                // Injeta os dados do filtro padrão na requisição para que o código abaixo os processe
                $request->merge($filtroPadrao->filtros);
            }
        }

        // 2. Inicia a Query com Eager Loading
        $query = Questao::with([
            'cargo.orgao',
            'cargo.banca',
            'cargo.ano',
            'materia',
            'assunto',
            'alternativas'
        ]);

        // 3. Aplica os filtros usando whereIn (suporta múltipla seleção)
        // Órgão
        if ($request->filled('orgao_id') && is_array($request->orgao_id) && !empty($request->orgao_id)) {
            $query->whereHas('cargo', function ($q) use ($request) {
                $q->whereIn('orgao_id', $request->orgao_id);
            });
        }

        // Banca
        if ($request->filled('banca_id') && is_array($request->banca_id) && !empty($request->banca_id)) {
            $query->whereHas('cargo', function ($q) use ($request) {
                $q->whereIn('banca_id', $request->banca_id);
            });
        }

        // Ano
        if ($request->filled('ano_id') && is_array($request->ano_id) && !empty($request->ano_id)) {
            $query->whereHas('cargo', function ($q) use ($request) {
                $q->whereIn('ano_id', $request->ano_id);
            });
        }

        // Cargo
        if ($request->filled('cargo_id') && is_array($request->cargo_id) && !empty($request->cargo_id)) {
            $query->whereIn('cargo_id', $request->cargo_id);
        }

        // Matéria
        if ($request->filled('materia_id') && is_array($request->materia_id) && !empty($request->materia_id)) {
            $query->whereIn('materia_id', $request->materia_id);
        }

        // Ordenação e Paginação
        $questoes = $query->inRandomOrder()->paginate(10);

        // 4. Busca os dados para preencher os selects do formulário
        return view('questoes.responder', [
            'questoes' => $questoes,
            'orgaos' => Orgao::orderBy('nome')->get(),
            'bancas' => Banca::orderBy('nome')->get(),
            'anos' => Ano::orderBy('ano', 'desc')->get(),
            'cargos' => Cargo::orderBy('nome')->get(),
            'materias' => Materia::orderBy('nome')->get(),
            'filtrosSalvos' => FiltroSalvo::where('user_id', $user->id)->orderBy('nome')->get(), // Para o dropdown de filtros salvos
        ]);
    }

    public function verificar(Request $request, LeitnerService $leitnerService)
    {
        // Carrega a questão junto para evitar uma nova query no banco
        $alternativa = Alternativa::with('questao')->findOrFail($request->alternativa_id);
        $user = Auth::user();

        // 1. Salva no histórico
        HistoricoResposta::create([
            'user_id' => $user->id,
            'questao_id' => $alternativa->questao_id,
            'alternativa_id' => $alternativa->id,
            'acertou' => $alternativa->correta,
            'respondido_em' => now()
        ]);

        // 2. ATUALIZA A CAIXA DO SISTEMA LEITNER (Repetição Espaçada)
        $leitnerService->processarResposta($user, $alternativa->questao, $alternativa->correta);

        // 3. Lógica do Caderno de Erros (se estiver errada e modal ativo)
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

    // Novo método para salvar os dados do modal
    public function salvarMotivoErro(Request $request, CadernoErro $erro)
    {
        // Garante que o erro pertence ao usuário
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

        // Validação básica
        $request->validate([
            'nome_filtro' => 'required|string|max:255',
            'definir_padrao' => 'nullable|boolean', // Checkbox opcional
        ]);

        // Coleta apenas os campos de filtro que foram enviados
        $filtrosData = $request->only(['orgao_id', 'banca_id', 'ano_id', 'cargo_id', 'materia_id']);

        // Remove arrays vazios para não salvar lixo no banco
        $filtrosData = array_filter($filtrosData, function ($item) {
            return !empty($item);
        });

        // Se o usuário marcou "Definir como padrão", removemos o padrão anterior dele
        if ($request->has('definir_padrao')) {
            FiltroSalvo::where('user_id', $user->id)->update(['is_padrao' => false]);
        }

        // Cria o novo filtro
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
