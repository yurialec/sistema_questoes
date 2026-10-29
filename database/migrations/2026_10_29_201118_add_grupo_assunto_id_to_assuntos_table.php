<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assuntos', function (Blueprint $table) {
            $table->foreignId('grupo_assunto_id')
                ->nullable()
                ->after('materia_id')
                ->constrained('grupos_assunto')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assuntos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('grupo_assunto_id');
        });
    }
};