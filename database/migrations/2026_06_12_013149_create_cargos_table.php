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
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orgao_id')
                ->constrained('orgaos')
                ->cascadeOnDelete();

            $table->foreignId('banca_id')
                ->constrained('bancas')
                ->cascadeOnDelete();

            $table->foreignId('ano_id')
                ->constrained('anos')
                ->cascadeOnDelete();

            $table->string('nome');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};
