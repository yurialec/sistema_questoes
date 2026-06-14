<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <--- Não esqueça de importar a Facade DB
use Illuminate\Support\Facades\File;
use App\Models\Orgao;
use App\Models\Banca;
use App\Models\Ano;
use App\Models\Cargo;
use App\Models\Materia;
use App\Models\Assunto;
use App\Models\Questao;
use App\Models\Alternativa;
use App\Models\TextoComplementar;

class JsonQuestoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $arquivos = File::files(database_path('imports'));

        foreach ($arquivos as $arquivo) {

            DB::transaction(function () use ($arquivo) {

                $json = json_decode(File::get($arquivo), true);

                if (!is_array($json)) {
                    return; // Pula arquivos inválidos dentro da closure
                }

                $orgao = Orgao::firstOrCreate(['nome' => $json['orgao']]);
                $banca = Banca::firstOrCreate(['nome' => $json['banca']]);
                $ano = Ano::firstOrCreate(['ano' => $json['ano']]);

                $cargo = Cargo::firstOrCreate(
                    ['nome' => $json['cargo'], 'ano_id' => $ano->id],
                    ['orgao_id' => $orgao->id, 'banca_id' => $banca->id]
                );

                $materia = Materia::firstOrCreate(
                    ['nome' => $json['materia']],
                    ['tipo' => $json['tipo_materia']]
                );

                foreach ($json['questoes'] as $dadosQuestao) {

                    $assunto = Assunto::firstOrCreate([
                        'materia_id' => $materia->id,
                        'nome' => $dadosQuestao['assunto']
                    ]);

                    // Lógica para Texto Complementar
                    $textoComplementarId = null;
                    $textoCompData = $dadosQuestao['texto_complementar'] ?? null;

                    if ($textoCompData && isset($textoCompData['conteudo'])) {
                        $textoComplementar = TextoComplementar::firstOrCreate(
                            ['conteudo' => $textoCompData['conteudo']]
                        );
                        $textoComplementarId = $textoComplementar->id;
                    }

                    $questao = Questao::updateOrCreate(
                        ['codigo' => $dadosQuestao['codigo']],
                        [
                            'cargo_id' => $cargo->id,
                            'materia_id' => $materia->id,
                            'assunto_id' => $assunto->id,
                            'imagem' => $dadosQuestao['imagem'] ?? null,
                            'tabela_html' => $dadosQuestao['tabela_html'] ?? null,
                            'texto_complementar_id' => $textoComplementarId,
                            'enunciado' => $dadosQuestao['enunciado'],
                            // 'dificuldade' => $dadosQuestao['dificuldade'] ?? null,
                        ]
                    );

                    foreach ($dadosQuestao['alternativas'] as $alternativa) {
                        Alternativa::updateOrCreate(
                            [
                                'questao_id' => $questao->id,
                                'letra' => $alternativa['letra']
                            ],
                            [
                                'descricao' => $alternativa['descricao'],
                                'correta' => ($alternativa['letra'] === $dadosQuestao['gabarito']),
                                'imagens' => $alternativa['imagens'] ?? null
                            ]
                        );
                    }
                }
            });
        }
    }
}
