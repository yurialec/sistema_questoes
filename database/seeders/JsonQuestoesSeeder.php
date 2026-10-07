<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
    public function run(): void
    {
        $arquivos = File::files(database_path('imports'));

        foreach ($arquivos as $arquivo) {
            try {
                $json = json_decode(File::get($arquivo), true);

                if (!is_array($json)) {
                    $this->command->warn("Arquivo inválido pulado: " . $arquivo->getFilename());
                    continue;
                }

                $blocos = array_is_list($json) ? $json : [$json];

                foreach ($blocos as $bloco) {
                    DB::transaction(function () use ($bloco, $arquivo) {
                        $this->importarBloco($bloco, $arquivo->getFilename());
                    });
                }

                $this->command->info("Importado com sucesso: " . $arquivo->getFilename());
            } catch (\Throwable $e) {
                $this->command->error('Erro em ' . $arquivo->getFilename());
                $this->command->error($e->getMessage());
            }
        }
    }

    private function importarBloco(array $bloco, string $nomeArquivo): void
    {
        try {
            // ---- Órgão / Banca / Ano: reutiliza se já existir ----
            $orgao = Orgao::firstOrCreate(['nome' => $bloco['orgao']]);
            $banca = Banca::firstOrCreate(['nome' => $bloco['banca']]);
            $ano   = Ano::firstOrCreate(['ano'   => $bloco['ano']]);

            // ---- Cargo: único por (nome + orgao_id) ----
            $cargo = Cargo::firstOrCreate([
                'nome'     => $bloco['cargo'],
                'orgao_id' => $orgao->id,
            ]);

            // ---- Matéria ----
            $materia = Materia::firstOrCreate(
                ['nome' => $bloco['materia']],
                ['tipo' => $bloco['tipo_materia']]
            );

            foreach ($bloco['questoes'] as $dadosQuestao) {
                // ---- Assunto ----
                $assunto = Assunto::firstOrCreate([
                    'materia_id' => $materia->id,
                    'nome'       => $dadosQuestao['assunto'],
                ]);

                // ---- Texto complementar (opcional) ----
                $textoComplementarId = null;
                $tcData = $dadosQuestao['texto_complementar'] ?? null;

                if (is_array($tcData) && !empty($tcData['conteudo'])) {
                    $tc = TextoComplementar::firstOrCreate([
                        'conteudo' => $tcData['conteudo'],
                    ]);
                    $textoComplementarId = $tc->id;
                }

                // ---- Questão: chave única = codigo ----
                $questao = Questao::updateOrCreate(
                    ['codigo' => $dadosQuestao['codigo']],
                    [
                        'cargo_id'              => $cargo->id,
                        'ano_id'                => $ano->id,
                        'banca_id'              => $banca->id,
                        'materia_id'            => $materia->id,
                        'assunto_id'            => $assunto->id,
                        'imagem'                => $dadosQuestao['imagem'] ?? null,
                        'tabela_html'           => $dadosQuestao['tabela_html'] ?? null,
                        'texto_complementar_id' => $textoComplementarId,
                        'enunciado'             => $dadosQuestao['enunciado'],
                    ]
                );

                // ---- Alternativas: limpa e recria (mais simples e seguro) ----
                Alternativa::where('questao_id', $questao->id)->delete();

                foreach ($dadosQuestao['alternativas'] as $alternativa) {
                    Alternativa::create([
                        'questao_id' => $questao->id,
                        'letra'      => $alternativa['letra'],
                        'descricao'  => $alternativa['descricao'],
                        'correta'    => ($alternativa['letra'] === $dadosQuestao['gabarito']),
                        'imagens'    => $alternativa['imagens'] ?? null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("Erro ao importar bloco do arquivo {$nomeArquivo}: " . $e->getMessage(), [
                'arquivo' => $nomeArquivo,
                'bloco'   => $bloco,
                'trace'   => $e->getTraceAsString(),
            ]);

            throw new \Exception(
                "Falha ao importar bloco do arquivo '{$nomeArquivo}': " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
}