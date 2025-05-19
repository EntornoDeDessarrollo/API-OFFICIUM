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
        Schema::create('suscripcion', function (Blueprint $table) {
            $table->unsignedBigInteger('IDDesempleado');
            $table->unsignedBigInteger('IDCategoria');
            $table->primary(['IDDesempleado', 'IDCategoria']);

            $table->foreign('IDDesempleado')->references('IDDesempleado')->on('desempleados')->onDelete('cascade');
            $table->foreign('IDCategoria')->references('IDCategoria')->on('categorias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripcion');
    }
};
