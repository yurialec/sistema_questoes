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
        Schema::create('caderno_erros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questao_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alternativa_id')->constrained()->cascadeOnDelete(); // A alternativa ERRADA que o usuário marcou
            
            $table->boolean('foi_chute')->default(false);
            $table->boolean('erro_distraido')->default(false);
            $table->text('motivo_erro')->nullable(); // Preenchido no modal
            
            $table->text('como_resolver')->nullable(); // Preenchido depois pelo usuário
            
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
