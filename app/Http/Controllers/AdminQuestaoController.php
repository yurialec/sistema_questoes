<?php

namespace App\Http\Controllers;

use App\Models\Ano;
use App\Models\Banca;
use App\Models\Cargo;
use App\Models\Materia;
use App\Models\Orgao;
use App\Models\Questao;
use Illuminate\Http\Request;

class AdminQuestaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Questao::with([
            'cargo.orgao',
            'cargo.banca',
            'cargo.ano',
            'materia',
            'assunto'
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

        $questoes = $query->orderBy('numero', 'asc')->paginate(10);

        return view('admin.questoes.index', [
            'questoes' => $questoes,
            'orgaos' => Orgao::orderBy('nome')->get(),
            'bancas' => Banca::orderBy('nome')->get(),
            'anos' => Ano::orderBy('ano', 'desc')->get(),
            'cargos' => Cargo::orderBy('nome')->get(),
            'materias' => Materia::orderBy('nome')->get(),
        ]);
    }

    public function edit($id)
    {
        $questao = Questao::with([
            'alternativas',
            'textoComplementar',
            'materia',
            'cargo',
            'assunto'
        ])->findOrFail($id);

        return view('admin.questoes.edit', [
            'questao' => $questao,
            'materias' => \App\Models\Materia::orderBy('nome')->get(),
            'assuntos' => \App\Models\Assunto::orderBy('nome')->get(),
            'cargos' => \App\Models\Cargo::with(['orgao', 'banca', 'ano'])->orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $questao = Questao::findOrFail($id);

        $questao->update([
            'materia_id' => $request->materia_id,
            'assunto_id' => $request->assunto_id,
            'cargo_id' => $request->cargo_id,
            'enunciado' => $request->enunciado,
            'tabela_html' => $request->tabela_html,
            'imagem' => $request->imagem,
        ]);

        if ($request->has('tem_texto_complementar') && !empty($request->texto_complementar)) {
            $textoComp = $questao->textoComplementar ?? new \App\Models\TextoComplementar();
            $textoComp->conteudo = $request->texto_complementar;
            $textoComp->save();

            if (!$questao->texto_complementar_id) {
                $questao->texto_complementar_id = $textoComp->id;
                $questao->save();
            }
        } else {
            if ($questao->texto_complementar_id) {
                $questao->texto_complementar_id = null;
                $questao->save();
            }
        }

        if ($request->has('alternativas')) {
            foreach ($request->alternativas as $altData) {
                $altId = $altData['id'] ?? null;

                \App\Models\Alternativa::updateOrCreate(
                    ['id' => $altId, 'questao_id' => $questao->id, 'letra' => $altData['letra']],
                    [
                        'descricao' => $altData['descricao'],
                        'correta' => isset($altData['correta']),
                        'imagens' => $altData['imagens'] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('admin.questoes.index')
            ->with('success', 'Questão atualizada com sucesso!');
    }

    public function uploadImagem(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $request->validate([
                'file' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $path = $file->store('questoes/imagens', 'public');

            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'Falha no upload da imagem'], 400);
    }
}
