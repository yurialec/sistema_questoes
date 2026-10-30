<?php

namespace Database\Seeders;

use App\Models\Assunto;
use App\Models\GrupoAssunto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GrupoAssuntoSeeder extends Seeder
{
    /**
     * Agrupamento manual dos assuntos existentes.
     *
     * Chave: nome do grupo
     * Valor: IDs dos assuntos pertencentes ao grupo
     *
     * Não utiliza regex nem classificação automática.
     */
    private array $grupos = [

        // ==========================================
        // ATUALIDADES DO MERCADO FINANCEIRO
        // ==========================================

        'Inovação e Tecnologia Financeira' => [
            1, 4, 76, 77, 78, 79,
        ],

        'Sistema Monetário e Bancário' => [
            2, 5,
        ],

        'Pix e Meios de Pagamento' => [
            3, 199,
        ],

        // ==========================================
        // CONHECIMENTOS BANCÁRIOS
        // ==========================================

        'Responsabilidade Socioambiental e Conduta Bancária' => [
            6, 194, 195,
        ],

        'Legislação, Sigilo e Segurança Bancária' => [
            7, 10, 89, 182, 193, 224,
        ],

        'Política Monetária, Fiscal e Mercado Cambial' => [
            8, 85, 86, 87, 88, 197, 198, 200,
        ],

        'Produtos, Operações e Segmentos Bancários' => [
            9, 196, 201, 202,
        ],

        // ==========================================
        // LÍNGUA INGLESA
        // ==========================================

        'Inglês — Interpretação e Gramática' => [
            11, 12, 13, 14, 15,
            70, 124, 125, 126,
        ],

        // ==========================================
        // MATEMÁTICA
        // ==========================================

        'Matemática — Razões, Proporções e Porcentagem' => [
            16, 17, 20,
        ],

        'Matemática — Funções, Equações e Sequências' => [
            18, 19, 72, 73, 74,
        ],

        'Matemática — Lógica e Combinatória' => [
            71, 75,
        ],

        // ==========================================
        // LÍNGUA PORTUGUESA
        // ==========================================

        'Português — Interpretação, Vocabulário e Semântica' => [
            21, 23, 29, 68,
            122, 123, 183, 184, 186, 187,
        ],

        'Português — Gramática e Sintaxe' => [
            22, 24, 25, 26, 27, 28,
            69, 185, 188,
        ],

        // ==========================================
        // PROBABILIDADE E ESTATÍSTICA
        // ==========================================

        'Probabilidade e Distribuições' => [
            30, 31, 34,
            80, 81, 82, 84, 133,
        ],

        'Estatística Descritiva e Medidas' => [
            32, 33, 83,
            131, 132, 134,
        ],

        // ==========================================
        // TECNOLOGIA — INTELIGÊNCIA ARTIFICIAL
        // ==========================================

        'Aprendizado de Máquina e Inteligência Artificial' => [
            35, 42, 95, 96,
        ],

        'Big Data e Ciência de Dados' => [
            55, 64, 93, 97, 98, 116,
        ],

        // ==========================================
        // TECNOLOGIA — BANCO DE DADOS
        // ==========================================

        'Banco de Dados — Modelagem e Normalização' => [
            36, 47, 50, 54,
            119, 120, 145, 208,
        ],

        'Banco de Dados — SQL e Consultas' => [
            37, 39, 43, 44,
            107, 109, 110, 112,
        ],

        'Banco de Dados — Administração e SGBDs' => [
            62, 63, 106, 108,
            111, 113, 146, 147,
            180, 209, 223,
        ],

        // ==========================================
        // TECNOLOGIA — PROGRAMAÇÃO
        // ==========================================

        'Programação — Linguagens e Orientação a Objetos' => [
            40, 46, 48, 52, 59,
            91, 92, 94, 104, 114,
            117, 121, 141, 151, 152,
            155, 162, 174, 181, 206, 219,
        ],

        'Programação — Python e Bibliotecas' => [
            38, 41, 90, 99,
            105, 115, 158, 159,
        ],

        'Desenvolvimento Web e Mobile' => [
            45, 56, 57,
            100, 101, 102,
        ],

        // ==========================================
        // TECNOLOGIA — ALGORITMOS
        // ==========================================

        'Estruturas de Dados e Algoritmos' => [
            49, 51, 53, 60, 61,
            65, 66, 67, 118, 140,
            142, 207,
        ],

        // ==========================================
        // TECNOLOGIA — ENGENHARIA DE SOFTWARE
        // ==========================================

        'Engenharia de Software, Qualidade e Processos' => [
            143, 144, 150, 153, 154,
            156, 157, 163, 175, 177,
            179, 204, 205, 220, 222,
        ],

        'Governança e Gestão de TI' => [
            148, 149,
        ],

        'DevOps e Gestão de Configuração' => [
            58, 103, 176, 203, 221,
        ],

        // ==========================================
        // TECNOLOGIA — INFRAESTRUTURA
        // ==========================================

        'Arquitetura e Sistemas Operacionais' => [
            165, 166, 167, 168,
            169, 170, 210, 211,
            212, 213, 215, 217,
        ],

        'Redes e Protocolos de Comunicação' => [
            160, 161, 171, 172,
            173, 178, 214, 216, 218,
        ],

        // ==========================================
        // MATEMÁTICA FINANCEIRA
        // ==========================================

        'Matemática Financeira — Juros e Taxas' => [
            127, 128, 130, 190, 191,
        ],

        'Matemática Financeira — Amortização e Séries' => [
            129, 189, 192,
        ],

        // ==========================================
        // ÉTICA E COMPLIANCE
        // ==========================================

        'Ética, LGPD e Responsabilidade Socioambiental' => [
            135, 136, 137, 138, 139,
        ],

        // ==========================================
        // CONHECIMENTOS E COMPORTAMENTOS DIGITAIS
        // ==========================================

        'Competências Comportamentais e Digitais' => [
            164,
        ],
    ];

    public function run(): void
    {
        DB::transaction(function () {

            // 1. Valida o mapeamento antes de alterar dados.

            $idsMapeados = collect($this->grupos)
                ->flatten()
                ->map(fn ($id) => (int) $id);

            $duplicados = $idsMapeados
                ->duplicates()
                ->unique()
                ->values();

            if ($duplicados->isNotEmpty()) {
                throw new RuntimeException(
                    'IDs duplicados no mapeamento: ' .
                    $duplicados->implode(', ')
                );
            }

            $idsExistentes = Assunto::query()
                ->pluck('id');

            $naoMapeados = $idsExistentes
                ->diff($idsMapeados)
                ->values();

            if ($naoMapeados->isNotEmpty()) {
                throw new RuntimeException(
                    'Existem assuntos sem grupo definido. IDs: ' .
                    $naoMapeados->implode(', ')
                );
            }

            // 2. Remove os vínculos existentes antes
            // de apagar os grupos.

            DB::table('assuntos')->update([
                'grupo_assunto_id' => null,
            ]);

            // 3. Limpa a tabela de grupos.
            // DELETE respeita as constraints do banco
            // e funciona dentro da transação.

            DB::table('grupos_assunto')->delete();

            $this->command?->info(
                'Tabela grupos_assunto limpa.'
            );

            // 4. Recria os grupos e seus vínculos.

            $totalGrupos = 0;
            $totalVinculados = 0;

            foreach ($this->grupos as $nome => $ids) {

                $grupo = GrupoAssunto::create([
                    'nome' => $nome,
                ]);

                $quantidade = Assunto::query()
                    ->whereIn('id', $ids)
                    ->update([
                        'grupo_assunto_id' => $grupo->id,
                    ]);

                $totalGrupos++;
                $totalVinculados += $quantidade;

                $this->command?->line(
                    "{$nome}: {$quantidade} assuntos"
                );
            }

            // 5. Confirma a integridade dos vínculos.

            $pendentes = Assunto::query()
                ->whereNull('grupo_assunto_id')
                ->count();

            if ($pendentes > 0) {
                throw new RuntimeException(
                    "{$pendentes} assuntos ficaram sem grupo."
                );
            }

            $this->command?->info(
                "Grupos criados: {$totalGrupos}"
            );

            $this->command?->info(
                "Assuntos vinculados: {$totalVinculados}"
            );

            $this->command?->info(
                'Agrupamento concluído com sucesso.'
            );
        });
    }
}
