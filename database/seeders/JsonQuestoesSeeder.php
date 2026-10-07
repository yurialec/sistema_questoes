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
        Orgao::where('nome', $bloco['orgao'])->delete();
        $orgao = Orgao::create(['nome' => $bloco['orgao']]);

        Banca::where('nome', $bloco['banca'])->delete();
        $banca = Banca::create(['nome' => $bloco['banca']]);

        Ano::where('ano', $bloco['ano'])->delete();
        $ano = Ano::create(['ano' => $bloco['ano']]);

        Cargo::where('nome', $bloco['cargo'])->where('ano_id', $ano->id)->delete();
        $cargo = Cargo::create([
            'nome' => $bloco['cargo'],
            'ano_id' => $ano->id,
            'orgao_id' => $orgao->id,
            'banca_id' => $banca->id,
        ]);

        Materia::where('nome', $bloco['materia'])->delete();
        $materia = Materia::create([
            'nome' => $bloco['materia'],
            'tipo' => $bloco['tipo_materia'],
        ]);

        foreach ($bloco['questoes'] as $dadosQuestao) {
            Assunto::where('materia_id', $materia->id)->where('nome', $dadosQuestao['assunto'])->delete();
            $assunto = Assunto::create([
                'materia_id' => $materia->id,
                'nome'       => $dadosQuestao['assunto'],
            ]);

            $textoComplementarId = null;
            $textoCompData = $dadosQuestao['texto_complementar'] ?? null;

            if ($textoCompData && isset($textoCompData['conteudo'])) {
                TextoComplementar::where('conteudo', $textoCompData['conteudo'])->delete();
                $textoComplementar = TextoComplementar::create([
                    'conteudo' => $textoCompData['conteudo'],
                ]);
                $textoComplementarId = $textoComplementar->id;
            }

            Questao::where('codigo', $dadosQuestao['codigo'])->delete();
            $questao = Questao::create([
                'codigo'                => $dadosQuestao['codigo'],
                'cargo_id'              => $cargo->id,
                'materia_id'            => $materia->id,
                'assunto_id'            => $assunto->id,
                'imagem'                => $dadosQuestao['imagem'] ?? null,
                'tabela_html'           => $dadosQuestao['tabela_html'] ?? null,
                'texto_complementar_id' => $textoComplementarId,
                'enunciado'             => $dadosQuestao['enunciado'],
            ]);

            foreach ($dadosQuestao['alternativas'] as $alternativa) {
                Alternativa::where('questao_id', $questao->id)->where('letra', $alternativa['letra'])->delete();
                Alternativa::create([
                    'questao_id' => $questao->id,
                    'letra'      => $alternativa['letra'],
                    'descricao'  => $alternativa['descricao'],
                    'correta'    => ($alternativa['letra'] === $dadosQuestao['gabarito']),
                    'imagens'    => $alternativa['imagens'] ?? null,
                ]);
            }
        }
    }
}