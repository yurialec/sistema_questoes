<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\JsonQuestoesImporter;

class JsonQuestoesSeeder extends Seeder
{
    public function run(): void
    {
        $arquivos = File::files(database_path('imports'));
        $importador = app(JsonQuestoesImporter::class);

        $totalGeralQuestoes = 0;

        foreach ($arquivos as $arquivo) {
            try {
                $json = json_decode(File::get($arquivo), true);

                if (!is_array($json)) {
                    $this->command->warn("Arquivo inválido pulado: " . $arquivo->getFilename());
                    continue;
                }

                $blocos = array_is_list($json) ? $json : [$json];

                $totalQuestoesArquivo = 0;

                foreach ($blocos as $bloco) {
                    $qtd = DB::transaction(function () use ($bloco, $arquivo, $importador) {
                        return $importador->importarBloco($bloco, $arquivo->getFilename());
                    });

                    $totalQuestoesArquivo += $qtd;
                }

                $totalGeralQuestoes += $totalQuestoesArquivo;

                $this->command->info(
                    "Importado com sucesso: {$arquivo->getFilename()} ({$totalQuestoesArquivo} questões)"
                );
            } catch (\Throwable $e) {
                $this->command->error('Erro em ' . $arquivo->getFilename());
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Total de questões importadas: {$totalGeralQuestoes}");
    }
}