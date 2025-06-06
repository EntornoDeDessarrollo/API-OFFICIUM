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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('IDDocumento');
            $table->unsignedBigInteger('IDUsuario');
            $table->unsignedBigInteger('IDPublicacion')->nullable();
            $table->string('Descripcion')->nullable();
            $table->enum('Tipo', ['Foto', 'Video', 'PDF', 'Publicacion']);
            $table->string('NombreArchivo');
            $table->string('URL');
            $table->timestamp('FechaSubida');
            $table->timestamps();

            $table->foreign('IDUsuario')->references('IDUsuario')->on('users')->onDelete('cascade');
            $table->foreign('IDPublicacion')->references('IDPublicacion')->on('publicacions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
