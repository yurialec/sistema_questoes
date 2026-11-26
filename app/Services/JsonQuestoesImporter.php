<?php

namespace App\Services;

use App\Models\Alternativa;
use App\Models\Ano;
use App\Models\Assunto;
use App\Models\Banca;
use App\Models\Cargo;
use App\Models\Materia;
use App\Models\Orgao;
use App\Models\Questao;
use App\Models\TextoComplementar;
use Illuminate\Support\Facades\Log;

class JsonQuestoesImporter
{
    public function importarBloco(array $bloco, string $nomeArquivo): int
    {
        try {
            $orgao = Orgao::firstOrCreate(['nome' => $bloco['orgao']]);
            $banca = Banca::firstOrCreate(['nome' => $bloco['banca']]);
            $ano = Ano::firstOrCreate(['ano' => $bloco['ano']]);

            $cargo = Cargo::firstOrCreate([
                'nome' => $bloco['cargo'],
                'orgao_id' => $orgao->id,
            ]);

            $materia = Materia::firstOrCreate(
                ['nome' => $bloco['materia']],
                ['tipo' => $bloco['tipo_materia']]
            );

            $questoesImportadas = 0;

            foreach ($bloco['questoes'] as $dadosQuestao) {
                $assunto = Assunto::firstOrCreate([
                    'materia_id' => $materia->id,
                    'nome' => $dadosQuestao['assunto'],
                ]);

                $textoComplementarId = null;
                $tcData = $dadosQuestao['texto_complementar'] ?? null;

                if (is_array($tcData) && !empty($tcData['conteudo'])) {
                    $tc = TextoComplementar::firstOrCreate([
                        'conteudo' => $tcData['conteudo'],
                    ]);
                    $textoComplementarId = $tc->id;
                }

                $questao = Questao::updateOrCreate(
                    ['codigo' => $dadosQuestao['codigo']],
                    [
                        'cargo_id' => $cargo->id,
                        'ano_id' => $ano->id,
                        'banca_id' => $banca->id,
                        'materia_id' => $materia->id,
                        'assunto_id' => $assunto->id,
                        'imagem' => $dadosQuestao['imagem'] ?? null,
                        'tabela_html' => $dadosQuestao['tabela_html'] ?? null,
                        'texto_complementar_id' => $textoComplementarId,
                        'enunciado' => $dadosQuestao['enunciado'],
                    ]
                );

                Alternativa::where('questao_id', $questao->id)->delete();

                foreach ($dadosQuestao['alternativas'] as $alternativa) {
                    Alternativa::create([
                        'questao_id' => $questao->id,
                        'letra' => $alternativa['letra'],
                        'descricao' => $alternativa['descricao'],
                        'correta' => ($alternativa['letra'] === $dadosQuestao['gabarito']),
                        'imagens' => $alternativa['imagens'] ?? null,
                    ]);
                }

                $questoesImportadas++;
            }

            return $questoesImportadas;
        } catch (\Throwable $exception) {
            Log::error("Erro ao importar bloco do arquivo {$nomeArquivo}: " . $exception->getMessage(), [
                'arquivo' => $nomeArquivo,
                'bloco' => $bloco,
                'trace' => $exception->getTraceAsString(),
            ]);

            throw new \Exception(
                "Falha ao importar bloco do arquivo '{$nomeArquivo}': " . $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }
}
