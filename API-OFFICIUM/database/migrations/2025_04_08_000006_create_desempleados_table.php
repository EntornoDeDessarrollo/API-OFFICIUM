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
        Schema::create('desempleados', function (Blueprint $table) {
            $table->id('IDDesempleado');
            $table->unsignedBigInteger('IDUsuario');
            $table->string('Nombre');
            $table->string('Apellido');
            $table->string('DNI');
            $table->text('Porfolios')->nullable();
            $table->enum('Disponibilidad', ['Tiempo completo', 'Medio tiempo', 'Temporal', 'Freelance']);
            $table->string('Ubicacion')->nullable();
            $table->string('Foto');
            $table->timestamps();

            $table->foreign('IDUsuario')->references('IDUsuario')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desempleados');
    }
};
