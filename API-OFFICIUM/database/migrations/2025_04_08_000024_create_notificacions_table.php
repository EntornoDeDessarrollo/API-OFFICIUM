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
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id('IDNotificacion');
            $table->unsignedBigInteger('IDUsuario');
            $table->string('Titulo');
            $table->text('Mensaje');
            $table->text('Ruta');
            $table->boolean('Leido')->default(false);
            $table->timestamp('FechaNotificacion');
            $table->timestamps();

            $table->foreign('IDUsuario')->references('IDUsuario')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};
