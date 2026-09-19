<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Array contendo os IDs das questões, a letra que deve ser anulada (0) 
     * e a letra que deve ser validada como o gabarito oficial (1).
     */
    private array $correcoes = [
        ['questao_id' => 45,  'anular_letra' => 'B', 'validar_letra' => 'D'],
        ['questao_id' => 49,  'anular_letra' => 'E', 'validar_letra' => 'C'],
        ['questao_id' => 52,  'anular_letra' => 'B', 'validar_letra' => 'A'],
        ['questao_id' => 122, 'anular_letra' => 'A', 'validar_letra' => 'B'],
        ['questao_id' => 126, 'anular_letra' => 'E', 'validar_letra' => 'D'],
    ];

    /**
     * Executa as correções (Aplica os updates).
     */
    public function up(): void
    {
        DB::transaction(function () {
            foreach ($this->correcoes as $item) {
                // 1. Anula a alternativa que estava incorreta no banco
                DB::table('alternativas')
                    ->where('questao_id', $item['questao_id'])
                    ->where('letra', $item['anular_letra'])
                    ->update(['correta' => 0, 'updated_at' => now()]);

                // 2. Valida a alternativa que é o gabarito oficial
                DB::table('alternativas')
                    ->where('questao_id', $item['questao_id'])
                    ->where('letra', $item['validar_letra'])
                    ->update(['correta' => 1, 'updated_at' => now()]);
            }
        });
    }

    /**
     * Reverte as correções (Rollback).
     */
    public function down(): void
    {
        DB::transaction(function () {
            foreach ($this->correcoes as $item) {
                // 1. Volta a validar a alternativa que tínhamos anulado
                DB::table('alternativas')
                    ->where('questao_id', $item['questao_id'])
                    ->where('letra', $item['anular_letra'])
                    ->update(['correta' => 1, 'updated_at' => now()]);

                // 2. Volta a anular a alternativa que tínhamos validado
                DB::table('alternativas')
                    ->where('questao_id', $item['questao_id'])
                    ->where('letra', $item['validar_letra'])
                    ->update(['correta' => 0, 'updated_at' => now()]);
            }
        });
    }
};
