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
        Schema::create('progresso_questoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');

            $table->tinyInteger('caixa_leitner')->default(1);
            $table->timestamp('proxima_revisao')->nullable();
            $table->timestamp('ultima_resposta')->nullable();

            $table->timestamps();
            $table->unique(['user_id', 'questao_id']);
            $table->index(['user_id', 'proxima_revisao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progresso_questoes');
    }
};
