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
        Schema::create('publicacions', function (Blueprint $table) {
            $table->id('IDPublicacion');
            $table->unsignedBigInteger('IDUsuario');
            $table->unsignedBigInteger('IDGrupo')->nullable(); // Campo IDGrupo nullable
            $table->text('Contenido');
            $table->timestamp('FechaPublicacion');
            $table->integer('Like')->default(0);
            $table->string('Archivo')->nullable();
            $table->enum('TipoArchivo', ['Foto', 'Video', 'PDF'])->nullable();
            $table->timestamps();

            $table->foreign('IDUsuario')->references('IDUsuario')->on('users')->onDelete('cascade');
            $table->foreign('IDGrupo')->references('IDGrupo')->on('grupos')->onDelete('cascade'); // O 'cascade' si la lógica lo requiere
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacions');
    }
};
