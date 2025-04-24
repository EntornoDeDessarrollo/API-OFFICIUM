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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id('IDEmpresa');
            $table->unsignedBigInteger('IDUsuario')->unique();
            $table->string('NombreEmpresa');
            $table->string('CIF');
            $table->unsignedBigInteger('IDSector');
            $table->string('Ubicacion');
            $table->string('SitioWeb')->nullable();
            $table->string('Foto');
            $table->timestamps();

            $table->foreign('IDUsuario')->references('IDUsuario')->on('users')->onDelete('cascade');
            $table->foreign('IDSector')->references('IDSector')->on('sectors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
