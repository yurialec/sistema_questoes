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
        Schema::create('metas_aprovacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Assumindo que a tabela de cargos se chama 'cargos'
            $table->foreignId('cargo_id')->constrained('cargos')->cascadeOnDelete(); 
            
            $table->foreignId('filtro_salvo_id')->constrained('filtros_salvos')->cascadeOnDelete();
            
            $table->integer('porcentagem')->default(0); // De 0 a 100
            $table->enum('rank', ['ruim', 'regular', 'bom', 'excelente'])->default('ruim');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas_aprovacao');
    }
};
