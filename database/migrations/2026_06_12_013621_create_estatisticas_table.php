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
        Schema::create('estatisticas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')
                ->nullable()
                ->constrained('materias')
                ->nullOnDelete();
            $table->unsignedInteger('acertos')
                ->default(0);
            $table->unsignedInteger('erros')
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatisticas');
    }
};
