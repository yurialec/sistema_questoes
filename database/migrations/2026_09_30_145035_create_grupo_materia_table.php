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
        Schema::create('grupo_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos_estudo')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained()->cascadeOnDelete();
            $table->unique(['grupo_id', 'materia_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_materia');
    }
};
