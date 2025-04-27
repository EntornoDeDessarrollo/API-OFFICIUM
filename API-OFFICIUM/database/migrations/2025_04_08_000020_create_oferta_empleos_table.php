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
        Schema::create('oferta_empleos', function (Blueprint $table) {
            $table->id('IDOferta');
            $table->unsignedBigInteger('IDEmpresa');
            $table->unsignedBigInteger('IDCategoria');
            $table->string('Titulo');
            $table->text('Descripcion');
            $table->string('Ubicacion');
            $table->string('Estado');
            $table->timestamp('FechaPublicacion');
            $table->timestamps();

            $table->foreign('IDEmpresa')->references('IDEmpresa')->on('empresas')->onDelete('cascade');
            $table->foreign('IDCategoria')->references('IDCategoria')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oferta_empleos');
    }
};
