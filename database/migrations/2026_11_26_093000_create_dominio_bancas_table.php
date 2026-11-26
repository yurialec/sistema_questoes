<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dominio_bancas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('banca_id')->constrained('bancas')->cascadeOnDelete();
            $table->unsignedInteger('qtd_erros')->default(0);
            $table->unsignedInteger('qtd_acertos')->default(0);
            $table->unsignedInteger('total_questoes_banca')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'banca_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dominio_bancas');
    }
};
