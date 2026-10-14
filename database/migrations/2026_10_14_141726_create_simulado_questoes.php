<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulado_questoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('simulado_id')->constrained('simulados')->cascadeOnDelete();
            $table->foreignId('questao_id')->constrained('questoes')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias');

            $table->unsignedInteger('ordem')->default(0);

            $table->foreignId('alternativa_id')->nullable()->constrained('alternativas')->nullOnDelete();
            $table->boolean('correta')->nullable();
            $table->timestamp('respondida_em')->nullable();

            $table->timestamps();

            $table->unique(['simulado_id', 'questao_id']);
            $table->index(['simulado_id', 'materia_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulado_questoes');
    }
};