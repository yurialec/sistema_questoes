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
        Schema::create('grade_estudos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('dia_semana')->comment('1=Segunda, 2=Terça, ..., 7=Domingo');
            $table->integer('ordem')->default(1)->comment('Ordem de prioridade no dia');
            $table->timestamps();

            // Garante que o usuário não repita a mesma matéria no mesmo dia
            $table->unique(['user_id', 'materia_id', 'dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_estudos');
    }
};
