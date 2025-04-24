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
        Schema::create('desempleado_idiomas', function (Blueprint $table) {
            $table->unsignedBigInteger('IDDesempleado');
            $table->unsignedBigInteger('IDIdiomaNivel');
            $table->primary(['IDDesempleado', 'IDIdiomaNivel']);

            $table->foreign('IDDesempleado')->references('IDDesempleado')->on('desempleados')->onDelete('cascade');
            $table->foreign('IDIdiomaNivel')->references('IDIdiomaNivel')->on('idioma_nivels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desempleado_idiomas');
    }
};
