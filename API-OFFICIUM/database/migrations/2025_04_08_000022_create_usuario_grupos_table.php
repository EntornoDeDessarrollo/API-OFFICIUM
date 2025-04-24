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
        Schema::create('usuario_grupos', function (Blueprint $table) {
            $table->unsignedBigInteger('IDUsuario');
            $table->unsignedBigInteger('IDGrupo');
            $table->primary(['IDUsuario', 'IDGrupo']);

            $table->foreign('IDUsuario')->references('IDUsuario')->on('users')->onDelete('cascade');
            $table->foreign('IDGrupo')->references('IDGrupo')->on('grupos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_grupos');
    }
};
