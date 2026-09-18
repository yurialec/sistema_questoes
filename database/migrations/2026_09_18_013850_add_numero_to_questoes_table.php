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
        Schema::table('questoes', function (Blueprint $table) {
            $table->integer('numero')->nullable()->after('id'); // ajuste a posição/tipo conforme seu caso
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questoes', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};
