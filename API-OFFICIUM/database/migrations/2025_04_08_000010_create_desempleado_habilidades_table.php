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
        Schema::create('desempleado_habilidades', function (Blueprint $table) {
            $table->unsignedBigInteger('IDDesempleado');
            $table->unsignedBigInteger('IDHabilidad');
            $table->primary(['IDDesempleado', 'IDHabilidad']);

            $table->foreign('IDDesempleado')->references('IDDesempleado')->on('desempleados')->onDelete('cascade');
            $table->foreign('IDHabilidad')->references('IDHabilidad')->on('habilidads')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desempleado_habilidades');
    }
};
