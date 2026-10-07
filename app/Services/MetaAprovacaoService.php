<?php

namespace App\Services;

use App\Models\Cargo;
use App\Models\FiltroSalvo;
use App\Models\MetaAprovacao;
use App\Models\Questao;
use Illuminate\Support\Facades\DB;
use Exception;

class MetaAprovacaoService
{
    /**
     * Cria uma nova meta de aprovação e o filtro vinculado.
     */
    public function criarMeta($userId, $cargoId)
    {
        // Verifica se já existe uma meta ativa para este usuário
        $metaExistente = MetaAprovacao::where('user_id', $userId)->first();
        if ($metaExistente) {
            throw new Exception('Você já possui uma meta ativa. Exclua a atual antes de criar uma nova.');
        }

        $cargo = Cargo::findOrFail($cargoId);

        // 1. Cria o filtro salvo automaticamente
        // A estrutura do array 'filtros' segue o padrão do seu sistema (arrays)
        $filtroSalvo = FiltroSalvo::create([
            'user_id'   => $userId,
            'nome'      => 'Meta Analista ' . $cargo->nome,
            'filtros'   => [
                'cargo_id' => [$cargoId] // Array, conforme seu sistema
            ],
            'is_padrao' => false,
        ]);

        // 2. Cria a meta de aprovação vinculando o filtro
        $meta = MetaAprovacao::create([
            'user_id'         => $userId,
            'cargo_id'        => $cargoId,
            'filtro_salvo_id' => $filtroSalvo->id,
            'porcentagem'     => 0,
            'rank'            => 'ruim',
        ]);

        return $meta->load('cargo', 'filtroSalvo');
    }

    /**
     * Busca a meta ativa do usuário.
     */
    public function getMetaAtiva($userId)
    {
        return MetaAprovacao::with('cargo', 'filtroSalvo')
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Exclui a meta ativa do usuário e o filtro vinculado.
     */
    public function excluirMeta($userId)
    {
        $meta = MetaAprovacao::where('user_id', $userId)->first();

        if (!$meta) {
            throw new Exception('Nenhuma meta ativa encontrada.');
        }

        // Exclui o filtro vinculado
        $meta->filtroSalvo()->delete();

        // Exclui a meta
        $meta->delete();

        return true;
    }

    /**
     * Recalcula o progresso da meta com base no histórico de respostas.
     * Chamado sempre que o usuário responde uma questão.
     */
    public function recalcularProgresso($userId)
    {
        $meta = MetaAprovacao::where('user_id', $userId)->first();

        if (!$meta) {
            return null; // Usuário não tem meta ativa
        }

        // Busca as estatísticas do usuário para o cargo da meta
        $stats = DB::table('historico_respostas as hr')
            ->join('questoes as q', 'hr.questao_id', '=', 'q.id')
            ->where('hr.user_id', $userId)
            ->where('q.cargo_id', $meta->cargo_id)
            ->selectRaw('COUNT(hr.id) as total_respondidas')
            ->selectRaw('SUM(CASE WHEN hr.acertou = 1 THEN 1 ELSE 0 END) as total_acertos')
            ->first();

        $totalRespondidas = $stats->total_respondidas ?? 0;
        $totalAcertos = $stats->total_acertos ?? 0;

        // Calcula a porcentagem
        if ($totalRespondidas === 0) {
            $porcentagem = 0;
        } else {
            $porcentagem = round(($totalAcertos / $totalRespondidas) * 100);
        }

        // Define o rank baseado na porcentagem
        $rank = $this->calcularRank($porcentagem);

        // Atualiza a meta
        $meta->update([
            'porcentagem' => $porcentagem,
            'rank'        => $rank,
        ]);

        return $meta->fresh();
    }

    /**
     * Define o rank com base na porcentagem.
     * Os thresholds podem ser ajustados conforme necessário.
     */
    private function calcularRank(int $porcentagem): string
    {
        if ($porcentagem >= 80) {
            return 'excelente';
        } elseif ($porcentagem >= 60) {
            return 'bom';
        } elseif ($porcentagem >= 40) {
            return 'regular';
        }

        return 'ruim';
    }
}