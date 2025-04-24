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
        Schema::create('experiencias', function (Blueprint $table) {
            $table->id('IDExperiencia');
            $table->unsignedBigInteger('IDDesempleado');
            $table->string('Empresa');
            $table->string('Puesto');
            $table->string('Duracion');
            $table->timestamps();

            $table->foreign('IDDesempleado')->references('IDDesempleado')->on('desempleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiencias');
    }
};
