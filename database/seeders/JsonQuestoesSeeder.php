<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

                // Normaliza para sempre trabalhar com um array de blocos.
                // Formato antigo (2021): objeto único  → { orgao, banca, materia, questoes... }
                // Formato novo  (2023): array de blocos → [{ orgao, banca, materia, questoes... }, ...]
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

    /**
     * Importa um único bloco de matéria (com suas questões).
     * Um "bloco" é o objeto que contém orgao, banca, ano, cargo, materia e questoes[].
     */
    private function importarBloco(array $bloco, string $nomeArquivo): void
    {
        $orgao  = Orgao::firstOrCreate(['nome' => $bloco['orgao']]);
        $banca  = Banca::firstOrCreate(['nome' => $bloco['banca']]);
        $ano    = Ano::firstOrCreate(['ano'   => $bloco['ano']]);

        $cargo = Cargo::firstOrCreate(
            ['nome' => $bloco['cargo'], 'ano_id' => $ano->id],
            ['orgao_id' => $orgao->id, 'banca_id' => $banca->id]
        );

        $materia = Materia::firstOrCreate(
            ['nome' => $bloco['materia']],
            ['tipo' => $bloco['tipo_materia']]
        );

        foreach ($bloco['questoes'] as $dadosQuestao) {
            $assunto = Assunto::firstOrCreate([
                'materia_id' => $materia->id,
                'nome'       => $dadosQuestao['assunto'],
            ]);

            // Texto complementar: pode ser null, ausente ou { conteudo: "..." }
            $textoComplementarId = null;
            $textoCompData = $dadosQuestao['texto_complementar'] ?? null;

            if ($textoCompData && isset($textoCompData['conteudo'])) {
                $textoComplementar   = TextoComplementar::firstOrCreate(
                    ['conteudo' => $textoCompData['conteudo']]
                );
                $textoComplementarId = $textoComplementar->id;
            }

            $questao = Questao::updateOrCreate(
                ['codigo' => $dadosQuestao['codigo']],
                [
                    'cargo_id'             => $cargo->id,
                    'materia_id'           => $materia->id,
                    'assunto_id'           => $assunto->id,
                    // 'numero'               => $dadosQuestao['numero'] ?? null,
                    'imagem'               => $dadosQuestao['imagem'] ?? null,
                    'tabela_html'          => $dadosQuestao['tabela_html'] ?? null,
                    'texto_complementar_id' => $textoComplementarId,
                    'enunciado'            => $dadosQuestao['enunciado'],
                    // 'dificuldade'       => $dadosQuestao['dificuldade'] ?? null,
                ]
            );

            foreach ($dadosQuestao['alternativas'] as $alternativa) {
                Alternativa::updateOrCreate(
                    [
                        'questao_id' => $questao->id,
                        'letra'      => $alternativa['letra'],
                    ],
                    [
                        'descricao' => $alternativa['descricao'],
                        'correta'   => ($alternativa['letra'] === $dadosQuestao['gabarito']),
                        'imagens'   => $alternativa['imagens'] ?? null,
                    ]
                );
            }
        }
    }
}
