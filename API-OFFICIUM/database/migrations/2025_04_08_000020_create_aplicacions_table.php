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
        Schema::create('aplicacions', function (Blueprint $table) {
            $table->id('IDAplicacion');
            $table->unsignedBigInteger('IDDesempleado');
            $table->unsignedBigInteger('IDOferta');
            $table->string('Estado');
            $table->timestamp('FechaAplicacion');
            $table->timestamps();

            $table->foreign('IDDesempleado')->references('IDDesempleado')->on('desempleados')->onDelete('cascade');
            $table->foreign('IDOferta')->references('IDOferta')->on('oferta_empleos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aplicacions');
    }
};
