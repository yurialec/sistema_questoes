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
        Schema::create('questoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_id')
                ->constrained('cargos')
                ->cascadeOnDelete();

            $table->foreignId('materia_id')
                ->constrained('materias')
                ->cascadeOnDelete();

            $table->foreignId('assunto_id')
                ->nullable()
                ->constrained('assuntos')
                ->nullOnDelete();

            $table->foreignId('texto_complementar_id')
                ->nullable()
                ->constrained('textos_complementares')
                ->nullOnDelete();

            $table->longText('enunciado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questoes');
    }
};
