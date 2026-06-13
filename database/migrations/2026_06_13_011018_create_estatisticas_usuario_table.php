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
        Schema::create('estatisticas_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('materia_id')
                ->nullable()
                ->constrained('materias')
                ->nullOnDelete();
            $table->integer('acertos')
                ->default(0);
            $table->integer('erros')
                ->default(0);
            $table->timestamps();

            $table->unique([
                'user_id',
                'materia_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatisticas_usuario');
    }
};
