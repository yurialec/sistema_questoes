<?php

namespace App\Services;

use App\Models\Cargo;
use App\Models\FiltroSalvo;
use App\Models\MetaAprovacao;
use Illuminate\Support\Facades\DB;
use Exception;

class MetaAprovacaoService
{
    public function criarMeta($userId, $cargoId, array $anos = [])
    {
        $metaExistente = MetaAprovacao::where('user_id', $userId)->first();
        if ($metaExistente) {
            throw new Exception('Você já possui uma meta ativa. Exclua a atual antes de criar uma nova.');
        }

        $cargo = Cargo::findOrFail($cargoId);

        $filtros = ['cargo_id' => [$cargoId]];
        if (!empty($anos)) {
            $filtros['ano_id'] = $anos;
        }

        $filtroSalvo = FiltroSalvo::create([
            'user_id'   => $userId,
            'nome'      => 'Meta ' . $cargo->nome,
            'filtros'   => $filtros,
            'is_padrao' => false,
        ]);

        $meta = MetaAprovacao::create([
            'user_id'         => $userId,
            'cargo_id'        => $cargoId,
            'filtro_salvo_id' => $filtroSalvo->id,
            'anos'            => !empty($anos) ? $anos : null,
            'porcentagem'     => 0,
            'rank'            => 'ruim',
        ]);

        return $meta->load('cargo', 'filtroSalvo');
    }

    public function getMetaAtiva($userId)
    {
        return MetaAprovacao::with('cargo', 'filtroSalvo')
            ->where('user_id', $userId)
            ->first();
    }

    public function excluirMeta($userId)
    {
        $meta = MetaAprovacao::where('user_id', $userId)->first();

        if (!$meta) {
            throw new Exception('Nenhuma meta ativa encontrada.');
        }

        if ($meta->filtro_salvo_id) {
            FiltroSalvo::where('id', $meta->filtro_salvo_id)
                ->where('user_id', $userId)
                ->delete();
        }

        $meta->delete();

        return true;
    }

    public function recalcularProgresso($userId)
    {
        $meta = MetaAprovacao::where('user_id', $userId)->first();

        if (!$meta) {
            return null;
        }

        $totalQuery = DB::table('questoes')->where('cargo_id', $meta->cargo_id);

        if (!empty($meta->anos)) {
            $totalQuery->whereIn('ano_id', $meta->anos);
        }

        $totalQuestoes = $totalQuery->count();

        $ultimasRespostas = DB::table('historico_respostas as hr')
            ->join('questoes as q', 'hr.questao_id', '=', 'q.id')
            ->where('hr.user_id', $userId)
            ->where('q.cargo_id', $meta->cargo_id)
            ->when(!empty($meta->anos), fn($q) => $q->whereIn('q.ano_id', $meta->anos))
            ->whereIn('hr.id', function ($sub) use ($userId) {
                $sub->selectRaw('MAX(hr2.id)')
                    ->from('historico_respostas as hr2')
                    ->where('hr2.user_id', $userId)
                    ->groupBy('hr2.questao_id');
            })
            ->selectRaw('SUM(CASE WHEN hr.acertou = 1 THEN 1 ELSE 0 END) as acertos')
            ->selectRaw('SUM(CASE WHEN hr.acertou = 0 THEN 1 ELSE 0 END) as erros')
            ->first();

        $acertos = (int) ($ultimasRespostas->acertos ?? 0);
        $erros   = (int) ($ultimasRespostas->erros ?? 0);
        $saldo   = $acertos - $erros;

        $porcentagem = $totalQuestoes > 0
            ? max(0, min(100, (int) round(($saldo / $totalQuestoes) * 100)))
            : 0;

        $meta->update([
            'porcentagem' => $porcentagem,
            'rank'        => $this->calcularRank($porcentagem),
        ]);

        return $meta->fresh();
    }

    private function calcularRank(int $porcentagem): string
    {
        if ($porcentagem >= 71) return 'aprovado';
        if ($porcentagem >= 36) return 'excelente';
        if ($porcentagem >= 16) return 'bom';
        if ($porcentagem >= 1)  return 'regular';
        return 'ruim';
    }
}
