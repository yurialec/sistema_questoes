<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('caderno_erros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questao_id')->constrained('questoes')->cascadeOnDelete();
            $table->foreignId('alternativa_id')->constrained('alternativas')->cascadeOnDelete();
            $table->boolean('foi_chute')->default(false);
            $table->boolean('erro_distraido')->default(false);
            $table->text('motivo_erro')->nullable();
            $table->text('como_resolver')->nullable();
            $table->enum('status', ['pendente', 'superado'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caderno_erros');
    }
};
