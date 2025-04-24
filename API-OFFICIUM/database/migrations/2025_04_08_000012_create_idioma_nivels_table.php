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
        Schema::create('idioma_nivels', function (Blueprint $table) {
            $table->id('IDIdiomaNivel');
            $table->unsignedBigInteger('IDIdioma');
            $table->string('Nivel');
            $table->timestamps();

            $table->foreign('IDIdioma')->references('IDIdiomas')->on('idiomas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idioma_nivels');
    }
};
