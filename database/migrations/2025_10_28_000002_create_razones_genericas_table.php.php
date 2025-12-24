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
        Schema::create('razones_genericas', function (Blueprint $table) {
            $table->id();

            // Título corto y visible en el admin
            $table->string('titulo');

            // Texto más largo opcional
            $table->text('descripcion')->nullable();

            // Para qué se usa
            $table->enum('tipo', ['reintegro', 'subsidio']);

            // Habilitar/deshabilitar sin borrar
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razones_genericas');
    }
};
