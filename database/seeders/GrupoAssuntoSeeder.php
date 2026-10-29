<?php

namespace Database\Seeders;

use App\Models\Assunto;
use App\Models\GrupoAssunto;
use Illuminate\Database\Seeder;

class GrupoAssuntoSeeder extends Seeder
{
    /**
     * Mapeamento matéria -> grupo. O sinal mais forte é a matéria.
     * A chave é o nome (case-insensitive) da matéria.
     */
    private array $gruposPorMateria = [
        'língua portuguesa'                        => 'Português',
        'língua inglesa'                           => 'Inglês',
        'matemática'                               => 'Matemática',
        'matemática financeira'                    => 'Matemática Financeira',
        'probabilidade e estatística'              => 'Estatística e Probabilidade',
        'noções de probabilidade e estatística'    => 'Estatística e Probabilidade',
        'conhecimentos bancários'                  => 'Conhecimentos Bancários',
        'atualidades do mercado financeiro'        => 'Atualidades do Mercado Financeiro',
        'comportamentos éticos e compliance'       => 'Ética e Compliance',
        'conhecimentos e comportamentos digitais'  => 'Conhecimentos Digitais',
    ];

    /**
     * Padrões (regex) aplicados ao nome do assunto, quando a matéria
     * não bater no mapa acima. A ORDEM importa: o primeiro match vence.
     */
    private array $gruposPorPadrao = [
        'Programação' => [
            '/(?:\b(?:java|python|typescript|kotlin|swift|c\+\+|cobol|\br\b|javascript|react native|android|html|desenvolvimento mobile|xcode|scikit-learn)\b|\bc#)/i',
            '/orientação a objetos|herança|polimorfismo|interfaces|tratamento de exceções|generics|hooks|interoperabilidade/i',
        ],
        'Banco de Dados' => [
            '/\b(banco de dados|sql|nosql|mongodb|modelo e-?r|modelo relacional|formas normais|mapeamento e-?r|sgbd|sgbdd|data warehouse|modelagem dimensional|índices|views|triggers|metadados|performance|restrições|ddl|produto cartesiano|schema flexibility|grafos)\b/i',
        ],
        'Estruturas de Dados e Algoritmos' => [
            '/(?:\b(?:pilha|pilhas|fila|filas|ordenação|algoritmos|algoritmos de ordenação|métodos de busca|busca binária|busca sequencial|bubble sort)\b|árvore|árvores)/i',
        ],
        'Engenharia de Software' => [
            '/\b(scrum|itil|cobit|cmmi|mps\.?br|uml|padrões de projeto|testes de software|tdd|test driven development|teste de unidade|user stories|story points|pontos de função|processo unificado|\bup\b|devops|ansible|controle de versão|gestão de configuração|generalização|diagrama de classes)\b/i',
        ],
        'Infraestrutura e Redes' => [
            '/\b(arquitetura de computadores|sistemas operacionais|linux|redes|tcp\/?ip|meios de transmissão|correio eletrônico|http|xml|rss|modos de endereçamento|sincronização de processos|memória virtual|\bucp\b|sistemas de numeração|ext2)\b/i',
        ],
        'Ciência de Dados e IA' => [
            '/\b(machine learning|aprendizado de máquina|big data|nlp|pandas|numpy|scipy|matplotlib|scikit-learn|derangements|dikw|\b3 vs\b|outliers|\bpca\b|tokenização|matriz de confusão|classificação|não supervisionado|pré-processamento|modelagem de dados|redução de dados)\b/i',
        ],
        'Conhecimentos Bancários' => [
            '/\b(taxa de juros real|mercado cambial|paridade dos juros|política fiscal|déficit primário|sigilo bancário|lc 105|lgpd|lei anticorrupção|crédito direto ao consumidor|política monetária|taxa de câmbio|pix|regime cambial|garantias de financiamento|fgts|código de conduta)\b/i',
            '/resolução\s+cmn\s+n?[ºo°]?\s*4\.?893/i',
        ],
    ];

    public function run(): void
    {
        // 1. Cria os grupos
        $todosOsGrupos = array_unique(array_merge(
            array_values($this->gruposPorMateria),
            array_keys($this->gruposPorPadrao)
        ));

        $mapaGrupos = [];
        foreach ($todosOsGrupos as $nome) {
            $mapaGrupos[$nome] = GrupoAssunto::firstOrCreate(['nome' => $nome]);
        }

        // 2. Percorre todos os assuntos e tenta atribuir um grupo
        $total = 0;
        $vinculados = 0;
        $naoVinculados = [];

        Assunto::with('materia')->chunkById(200, function ($assuntos) use ($mapaGrupos, &$total, &$vinculados, &$naoVinculados) {
            foreach ($assuntos as $assunto) {
                $total++;
                $grupoNome = $this->resolverGrupo($assunto);

                if ($grupoNome && isset($mapaGrupos[$grupoNome])) {
                    $assunto->grupo_assunto_id = $mapaGrupos[$grupoNome]->id;
                    $assunto->save();
                    $vinculados++;
                } else {
                    $naoVinculados[] = $assunto->id . ' - ' . $assunto->nome;
                }
            }
        });

        $this->command->info("Total de assuntos: {$total}");
        $this->command->info("Vinculados a um grupo: {$vinculados}");
        $this->command->warn("Não vinculados: " . count($naoVinculados));

        if (!empty($naoVinculados)) {
            foreach ($naoVinculados as $item) {
                $this->command->line("  - {$item}");
            }
        }
    }

    private function resolverGrupo(Assunto $assunto): ?string
    {
        $materia = mb_strtolower(trim($assunto->materia?->nome ?? ''));

        if (isset($this->gruposPorMateria[$materia])) {
            return $this->gruposPorMateria[$materia];
        }

        $nomeAssunto = $assunto->nome ?? '';
        foreach ($this->gruposPorPadrao as $grupo => $padroes) {
            foreach ($padroes as $regex) {
                if (preg_match($regex . 'u', $nomeAssunto)) {
                    return $grupo;
                }
            }
        }

        return null;
    }
}
