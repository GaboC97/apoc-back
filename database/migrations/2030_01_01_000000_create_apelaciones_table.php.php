<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apelaciones', function (Blueprint $table) {
            $table->id();

            // Tipo del reclamo
            $table->enum('tipo', ['reintegro', 'subsidio']);

            // Relación con reintegro o subsidio (una de las dos)
            $table->unsignedBigInteger('reintegro_id')->nullable();
            $table->unsignedBigInteger('subsidio_id')->nullable();

            // Usuario que apela
            $table->unsignedBigInteger('user_id');

            // Datos de la apelación
            $table->text('motivo');
            $table->string('archivo_path')->nullable();
            $table->string('estado')->default('pendiente'); 
            // estados: pendiente | aprobada | rechazada

            $table->timestamps();

            // Relaciones
            $table->foreign('reintegro_id')
                  ->references('id')->on('reintegros')
                  ->nullOnDelete();

            $table->foreign('subsidio_id')
                  ->references('id')->on('subsidios')
                  ->nullOnDelete();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apelaciones');
    }
};
