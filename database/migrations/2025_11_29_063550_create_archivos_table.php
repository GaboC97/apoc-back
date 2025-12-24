<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Renombramos la tabla a 'archivos' para que sea genérica
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();

            // CLAVE POLIMÓRFICA: Crea archivable_id (int) y archivable_type (string)
            // Estos dos campos sabrán si el archivo es de un Reintegro, Subsidio, etc.
            $table->morphs('archivable'); 
            
            $table->string('path'); // Ruta física en storage
            $table->string('nombre_original')->nullable(); // Nombre real del archivo
            $table->string('tipo_mime')->nullable(); // pdf, jpg, png (opcional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};