<?php

namespace Database\Seeders;

use App\Models\Alternativa;
use App\Models\Ano;
use App\Models\Banca;
use App\Models\Cargo;
use App\Models\Materia;
use App\Models\Orgao;
use App\Models\Questao;
use App\Models\Simulado;
use App\Models\SimuladoQuestao;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimuladoFinalizadoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('Nenhum usuário encontrado. Cadastre um usuário antes de rodar este seeder.');
            return;
        }

        // Tenta achar questões reais agrupadas por um filtro (cargo + banca + ano)
        $grupo = $this->buscarGrupoDeQuestoes();

        if ($grupo) {
            $this->command->info("Usando {$grupo->count()} questões reais do cargo #{$grupo->first()->cargo_id}.");
            $this->criarSimuladosComQuestoesReais($user, $grupo);
            return;
        }

        $this->command->warn('Nenhuma questão encontrada. Criando questões fake...');
        $questoes = $this->criarQuestoesFake();
        $this->criarSimuladosComQuestoesFake($user, $questoes);
    }

    protected function buscarGrupoDeQuestoes()
    {
        // Pega o grupo de questões mais numeroso por (cargo, banca, ano)
        $grupo = Questao::query()
            ->select('cargo_id', 'banca_id', 'ano_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('cargo_id')
            ->whereNotNull('banca_id')
            ->whereNotNull('ano_id')
            ->groupBy('cargo_id', 'banca_id', 'ano_id')
            ->having('total', '>=', 5)
            ->orderByDesc('total')
            ->first();

        if (!$grupo) {
            return null;
        }

        return Questao::where('cargo_id', $grupo->cargo_id)
            ->where('banca_id', $grupo->banca_id)
            ->where('ano_id', $grupo->ano_id)
            ->with(['materia', 'alternativas'])
            ->get();
    }

    protected function criarSimuladosComQuestoesReais(User $user, $questoes)
    {
        $percentuais = [0.6, 0.75, 0.9];
        $diasAtras   = [30, 15, 5];

        foreach ($percentuais as $i => $percentual) {
            $this->criarSimuladoFinalizado(
                user: $user,
                questoes: $questoes,
                percentualAcerto: $percentual,
                diasAtras: $diasAtras[$i],
                nome: 'Simulado finalizado — ' . ($i + 1)
            );
        }
    }

    protected function criarSimuladosComQuestoesFake(User $user, $questoes)
    {
        $percentuais = [0.6, 0.75, 0.9];
        $diasAtras   = [30, 15, 5];

        foreach ($percentuais as $i => $percentual) {
            $this->criarSimuladoFinalizado(
                user: $user,
                questoes: $questoes,
                percentualAcerto: $percentual,
                diasAtras: $diasAtras[$i],
                nome: 'Simulado finalizado (fake) — ' . ($i + 1)
            );
        }
    }

    protected function criarSimuladoFinalizado(
        User $user,
        $questoes,
        float $percentualAcerto,
        int $diasAtras,
        string $nome
    ): void {
        $totalQuestoes = $questoes->count();
        $qtdAcertos    = (int) round($totalQuestoes * $percentualAcerto);
        $qtdErros      = $totalQuestoes - $qtdAcertos;

        $criadoEm     = now()->subDays($diasAtras);
        $iniciadoEm   = $criadoEm->copy()->addMinutes(1);
        $finalizadoEm = $iniciadoEm->copy()->addMinutes(rand(90, 180));

        // Agrupa por matéria para ordenar igual ao fluxo real
        $agrupadas = $questoes
            ->sortBy(fn($q) => mb_strtolower($q->materia->nome ?? ''))
            ->groupBy('materia_id');

        DB::transaction(function () use (
            $user,
            $nome,
            $agrupadas,
            $totalQuestoes,
            $qtdAcertos,
            $qtdErros,
            $criadoEm,
            $iniciadoEm,
            $finalizadoEm
        ) {
            $simulado = Simulado::create([
                'user_id'          => $user->id,
                'nome'             => $nome,
                'filtros'          => [
                    'seed' => true,
                    'data' => $criadoEm->toDateTimeString(),
                ],
                'status'           => 'finalizado',
                'total_questoes'   => $totalQuestoes,
                'total_acertos'    => $qtdAcertos,
                'total_erros'      => $qtdErros,
                'iniciado_em'      => $iniciadoEm,
                'finalizado_em'    => $finalizadoEm,
                'created_at'       => $criadoEm,
                'updated_at'       => $finalizadoEm,
            ]);

            // Distribui acertos/erros aleatoriamente entre as questões
            $indicesAcerto = collect(range(0, $totalQuestoes - 1))
                ->shuffle()
                ->take($qtdAcertos)
                ->flip(); // chaves viram os índices, valor vira o índice original

            $ordem = 1;
            $indexGlobal = 0;

            foreach ($agrupadas as $materiaId => $questoesDaMateria) {
                foreach ($questoesDaMateria as $questao) {
                    $correta = $indicesAcerto->has($indexGlobal);

                    // Sorteia uma alternativa. Se for acerto, pega a correta.
                    // Se for erro, pega uma errada (ou qualquer uma caso não exista).
                    $alternativa = $this->escolherAlternativa($questao, $correta);

                    SimuladoQuestao::create([
                        'simulado_id'    => $simulado->id,
                        'questao_id'     => $questao->id,
                        'materia_id'     => $materiaId,
                        'ordem'          => $ordem++,
                        'alternativa_id' => $alternativa?->id,
                        'correta'        => $correta,
                        'respondida_em'  => $iniciadoEm->copy()->addMinutes($ordem * 2),
                        'created_at'     => $criadoEm,
                        'updated_at'     => $finalizadoEm,
                    ]);

                    $indexGlobal++;
                }
            }
        });

        $this->command->info("Simulado criado: {$nome} ({$totalQuestoes} questões, {$qtdAcertos} acertos, {$qtdErros} erros).");
    }

    protected function escolherAlternativa(Questao $questao, bool $correta): ?Alternativa
    {
        if ($questao->alternativas->isEmpty()) {
            return null;
        }

        if ($correta) {
            return $questao->alternativas->firstWhere('correta', true)
                ?? $questao->alternativas->first();
        }

        return $questao->alternativas->firstWhere('correta', false)
            ?? $questao->alternativas->first();
    }

    /**
     * Cria questões fake com 3 matérias e 4 alternativas cada.
     * Usado só quando não há questões reais no banco.
     */
    protected function criarQuestoesFake()
    {
        $orgao  = Orgao::firstOrCreate(['nome' => 'Órgão Fake']);
        $cargo  = Cargo::firstOrCreate(['orgao_id' => $orgao->id, 'nome' => 'Cargo Fake']);
        $banca  = Banca::firstOrCreate(['nome' => 'Banca Fake']);
        $ano    = Ano::firstOrCreate(['ano' => date('Y')]);

        $materiasNomes = ['Português', 'Matemática', 'Direito'];

        $questoes = collect();

        foreach ($materiasNomes as $nomeMateria) {
            $materia = Materia::firstOrCreate(['nome' => $nomeMateria]);

            for ($i = 1; $i <= 5; $i++) {
                $questao = Questao::create([
                    'codigo'     => 'FAKE-' . $materia->id . '-' . $i,
                    'cargo_id'   => $cargo->id,
                    'ano_id'     => $ano->id,
                    'banca_id'   => $banca->id,
                    'materia_id' => $materia->id,
                    'enunciado'  => "Questão fake #{$i} de {$nomeMateria}. Qual é a alternativa correta?",
                ]);

                foreach (['A', 'B', 'C', 'D'] as $idx => $letra) {
                    Alternativa::create([
                        'questao_id' => $questao->id,
                        'letra'      => $letra,
                        'descricao'  => "Alternativa {$letra} da questão {$i} de {$nomeMateria}.",
                        'correta'    => $idx === 0, // A é sempre correta
                    ]);
                }

                $questoes->push($questao->fresh(['materia', 'alternativas']));
            }
        }

        return $questoes;
    }
}
