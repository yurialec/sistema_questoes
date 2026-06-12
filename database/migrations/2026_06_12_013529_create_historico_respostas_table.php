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
        Schema::create('historico_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questao_id')
                ->constrained('questoes')
                ->cascadeOnDelete();
            $table->foreignId('alternativa_id')
                ->constrained('alternativas')
                ->cascadeOnDelete();
            $table->boolean('acertou');
            $table->timestamp('respondido_em');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_respostas');
    }
};
