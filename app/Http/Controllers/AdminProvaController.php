<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Services\JsonQuestoesImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminProvaController extends Controller
{
    public function index()
    {
        $provas = Questao::query()
            ->selectRaw('cargo_id, ano_id, banca_id, COUNT(*) AS questoes_count')
            ->with(['cargo.orgao', 'ano', 'banca'])
            ->groupBy('cargo_id', 'ano_id', 'banca_id')
            ->orderByDesc('ano_id')
            ->get();

        return view('admin.provas.index', compact('provas'));
    }

    public function cadastrar()
    {
        return view('admin.provas.cadastrar');
    }

    public function cadastrarProva(Request $request, JsonQuestoesImporter $importador)
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'max:10240'],
        ]);

        $arquivo = $request->file('arquivo');

        if (strtolower($arquivo->getClientOriginalExtension()) !== 'json') {
            return back()->withErrors([
                'arquivo' => 'Selecione um arquivo com extensão .json.',
            ]);
        }

        try {
            $json = json_decode(
                file_get_contents($arquivo->getRealPath()),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $exception) {
            return back()->withErrors([
                'arquivo' => 'O arquivo selecionado não contém um JSON válido.',
            ]);
        }

        if (!is_array($json) || $json === []) {
            return back()->withErrors([
                'arquivo' => 'O JSON deve conter um bloco de prova ou uma lista de blocos.',
            ]);
        }

        $blocos = array_is_list($json) ? $json : [$json];

        Validator::make(['blocos' => $blocos], [
            'blocos' => ['required', 'array', 'min:1'],
            'blocos.*' => ['required', 'array'],
            'blocos.*.orgao' => ['required', 'string'],
            'blocos.*.banca' => ['required', 'string'],
            'blocos.*.ano' => ['required', 'integer'],
            'blocos.*.cargo' => ['required', 'string'],
            'blocos.*.materia' => ['required', 'string'],
            'blocos.*.tipo_materia' => ['required', 'string'],
            'blocos.*.questoes' => ['required', 'array', 'min:1'],
            'blocos.*.questoes.*' => ['required', 'array'],
            'blocos.*.questoes.*.codigo' => ['required', 'string'],
            'blocos.*.questoes.*.assunto' => ['required', 'string'],
            'blocos.*.questoes.*.enunciado' => ['required', 'string'],
            'blocos.*.questoes.*.gabarito' => ['required', 'string'],
            'blocos.*.questoes.*.alternativas' => ['required', 'array', 'min:1'],
            'blocos.*.questoes.*.alternativas.*.letra' => ['required', 'string', 'size:1'],
            'blocos.*.questoes.*.alternativas.*.descricao' => ['required', 'string'],
        ])->validate();

        $totalQuestoes = 0;

        foreach ($blocos as $bloco) {
            $totalQuestoes += DB::transaction(
                fn () => $importador->importarBloco($bloco, $arquivo->getClientOriginalName())
            );
        }

        return redirect()
            ->route('admin.provas.index')
            ->with('success', "Importação concluída: {$totalQuestoes} questões cadastradas.");
    }
}
