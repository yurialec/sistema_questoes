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
        Schema::dropIfExists('grade_estudos');

        Schema::create('grade_estudos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos_estudo')->cascadeOnDelete();
            $table->tinyInteger('dia_semana')->comment('1=Segunda, 2=Terça, ..., 7=Domingo');
            $table->timestamps();

            $table->unique(['user_id', 'grupo_id', 'dia_semana']);
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
