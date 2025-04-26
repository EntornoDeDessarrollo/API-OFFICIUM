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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id('IDGrupo');
            $table->string('Nombre');
            $table->text('Descripcion')->nullable();
            $table->enum('Privacidad', ['Publico', 'Privado']);
            $table->string('Foto')->nullable();
            $table->unsignedBigInteger('Propietario'); // ID del usuario propietario
            $table->foreign('Propietario')->references('IDUsuario')->on('users')->onDelete('cascade'); // Clave foránea a users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
