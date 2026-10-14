<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nome')->nullable();
            $table->json('filtros')->nullable();

            $table->string('status')->default('nao_iniciado');

            $table->unsignedInteger('total_questoes')->default(0);
            $table->unsignedInteger('total_acertos')->nullable();
            $table->unsignedInteger('total_erros')->nullable();

            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('finalizado_em')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulados');
    }
};
