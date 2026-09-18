<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alternativa;
use App\Models\Ano;
use App\Models\Banca;
use App\Models\Cargo;
use App\Models\HistoricoResposta;
use App\Models\Materia;
use App\Models\Orgao;
use App\Models\Questao;
use Illuminate\Http\Request;

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
