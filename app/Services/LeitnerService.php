<?php

namespace App\Services;

use App\Models\ProgressoQuestao;
use App\Models\Questao;
use App\Models\User;
use Carbon\Carbon;

class LeitnerService
{
    /**
     * Processa a resposta do usuário e atualiza a caixa do sistema Leitner.
     *
     * @param User $user
     * @param Questao $questao
     * @param bool $acertou
     * @return ProgressoQuestao
     */
    public function processarResposta(User $user, Questao $questao, bool $acertou): ProgressoQuestao
    {
        // 1. Busca o progresso existente ou instancia um novo (caixa 1 por padrão)
        $progresso = ProgressoQuestao::firstOrNew([
            'user_id' => $user->id,
            'questao_id' => $questao->id,
        ]);

        // 2. Define a nova caixa com base no acerto ou erro
        if ($acertou) {
            // Se acertou, sobe uma caixa (máximo 5)
            $progresso->caixa_leitner = min($progresso->caixa_leitner + 1, 5);
        } else {
            // Se errou, volta imediatamente para a caixa 1 (reaprendizado)
            $progresso->caixa_leitner = 1;
        }

        // 3. Define o intervalo de dias para a próxima revisão com base na caixa atual
        // Você pode ajustar esses valores conforme a intensidade desejada para o estudo
        $intervalosDias = [
            1 => 1,  // Caixa 1: rever amanhã
            2 => 3,  // Caixa 2: rever em 3 dias
            3 => 7,  // Caixa 3: rever em 1 semana
            4 => 15, // Caixa 4: rever em 15 dias
            5 => 30, // Caixa 5: rever em 1 mês (questão dominada)
        ];

        $diasParaProximaRevisao = $intervalosDias[$progresso->caixa_leitner] ?? 1;

        // 4. Atualiza as datas e salva
        $progresso->ultima_resposta = now();
        $progresso->proxima_revisao = now()->addDays($diasParaProximaRevisao);
        
        $progresso->save();

        return $progresso;
    }
}