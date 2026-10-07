<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) adiciona as colunas em questoes
        Schema::table('questoes', function (Blueprint $table) {
            $table->foreignId('ano_id')->nullable()->after('cargo_id')
                ->constrained('anos')->nullOnDelete();
            $table->foreignId('banca_id')->nullable()->after('ano_id')
                ->constrained('bancas')->nullOnDelete();
        });

        // 2) copia os valores do cargo para as questoes
        DB::statement('UPDATE questoes
                        SET ano_id = (
                                SELECT c.ano_id
                                FROM cargos c
                                WHERE c.id = questoes.cargo_id
                            ),
                            banca_id = (
                                SELECT c.banca_id
                                FROM cargos c
                                WHERE c.id = questoes.cargo_id
                            )
                        WHERE cargo_id IS NOT NULL
            ');

        // 3) remove de cargos
        Schema::table('cargos', function (Blueprint $table) {
            $table->dropForeign(['ano_id']);
            $table->dropForeign(['banca_id']);
            $table->dropColumn(['ano_id', 'banca_id']);
        });

        // 4) deduplica cargos (mantém o menor id por nome+orgao_id)
        $duplicados = DB::table('cargos')
            ->select('nome', 'orgao_id', DB::raw('MIN(id) as manter_id'))
            ->groupBy('nome', 'orgao_id')
            ->get();

        foreach ($duplicados as $d) {
            $duplicadosIds = DB::table('cargos')
                ->where('nome', $d->nome)
                ->where('orgao_id', $d->orgao_id)
                ->where('id', '!=', $d->manter_id)
                ->pluck('id');

            if ($duplicadosIds->isEmpty()) {
                continue;
            }

            // reatribui questoes órfãs ao cargo que fica
            DB::table('questoes')
                ->whereIn('cargo_id', $duplicadosIds)
                ->update(['cargo_id' => $d->manter_id]);

            DB::table('cargos')->whereIn('id', $duplicadosIds)->delete();
        }

        // 5) garante unicidade
        Schema::table('cargos', function (Blueprint $table) {
            $table->unique(['nome', 'orgao_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cargos', function (Blueprint $table) {
            $table->dropUnique(['nome', 'orgao_id']);
            $table->foreignId('ano_id')->nullable()->constrained('anos');
            $table->foreignId('banca_id')->nullable()->constrained('bancas');
        });

        Schema::table('questoes', function (Blueprint $table) {
            $table->dropForeign(['ano_id']);
            $table->dropForeign(['banca_id']);
            $table->dropColumn(['ano_id', 'banca_id']);
        });
    }
};
