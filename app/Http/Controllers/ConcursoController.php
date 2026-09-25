<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alternativa;
use App\Models\Ano;
use App\Models\Banca;
use App\Models\CadernoErro;
use App\Models\Cargo;
use App\Models\HistoricoResposta;
use App\Models\Materia;
use App\Models\Orgao;
use App\Models\Questao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConcursoController extends Controller
{
    public function responder(Request $request)
    {
        $query = Questao::with([
            'cargo.orgao',
            'cargo.banca',
            'cargo.ano',
            'materia',
            'alternativas',
            'textoComplementar'
        ]);

        $query->when($request->filled('orgao_id'), function ($q) use ($request) {
            $q->whereHas('cargo', fn($cargo) => $cargo->where('orgao_id', $request->orgao_id));
        });

        $query->when($request->filled('banca_id'), function ($q) use ($request) {
            $q->whereHas('cargo', fn($cargo) => $cargo->where('banca_id', $request->banca_id));
        });

        $query->when($request->filled('ano_id'), function ($q) use ($request) {
            $q->whereHas('cargo', fn($cargo) => $cargo->where('ano_id', $request->ano_id));
        });

        $query->when($request->filled('cargo_id'), function ($q) use ($request) {
            $q->where('cargo_id', $request->cargo_id);
        });

        $query->when($request->filled('materia_id'), function ($q) use ($request) {
            $q->where('materia_id', $request->materia_id);
        });

        if (!$request->hasAny(['orgao_id', 'banca_id', 'ano_id', 'cargo_id', 'materia_id'])) {
            $query->inRandomOrder();
        } else {
            $query->orderBy('numero', 'asc');
        }

        $questoes = $query->paginate(10);

        return view('questoes.responder', [
            'questoes' => $questoes,
            'orgaos' => Orgao::orderBy('nome')->get(),
            'bancas' => Banca::orderBy('nome')->get(),
            'anos' => Ano::orderBy('ano', 'desc')->get(),
            'cargos' => Cargo::orderBy('nome')->get(),
            'materias' => Materia::orderBy('nome')->get(),
        ]);
    }

    public function verificar(Request $request)
    {
        $alternativa = Alternativa::findOrFail($request->alternativa_id);
        $user = $request->user();

        // 1. Registra no histórico normal
        HistoricoResposta::create([
            'user_id' => $user->id,
            'questao_id' => $alternativa->questao_id,
            'alternativa_id' => $alternativa->id,
            'acertou' => $alternativa->correta,
            'respondido_em' => now()
        ]);

        // 2. Lógica do Caderno de Erros
        if (!$alternativa->correta && $user->ativo_modal_erros) {
            $erro = CadernoErro::create([
                'user_id' => $user->id,
                'questao_id' => $alternativa->questao_id,
                'alternativa_id' => $alternativa->id,
                'status' => 'pendente'
            ]);

            // Redireciona de volta com dados para abrir o modal
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
}
