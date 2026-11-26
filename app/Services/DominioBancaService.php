<?php

namespace App\Services;

use App\Models\DominioBanca;
use App\Models\Questao;

class DominioBancaService
{
    public function registrarResposta(int $userId, Questao $questao, bool $acertou): void
    {
        if (!$questao->banca_id) {
            return;
        }

        $dominio = DominioBanca::query()
            ->where('user_id', $userId)
            ->where('banca_id', $questao->banca_id);

        if (!$dominio->exists()) {
            return;
        }

        $coluna = $acertou ? 'qtd_acertos' : 'qtd_erros';
        $totalQuestoes = Questao::where('banca_id', $questao->banca_id)->count();

        $dominio->increment($coluna, 1, [
            'total_questoes_banca' => $totalQuestoes,
        ]);
    }
}
