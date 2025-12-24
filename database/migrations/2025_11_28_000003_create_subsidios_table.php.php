<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subsidios', function (Blueprint $table) {
            $table->id();

            // Usuario solicitante
            $table->unsignedBigInteger('user_id');

            // Datos Personales
            $table->string('correo_electronico');
            $table->string('apellido_nombre');
            $table->string('dni');
            $table->string('telefono'); 
            $table->unsignedBigInteger('organismo_id'); 

            // Datos Bancarios
            $table->string('cbu_alias');

            // Datos del Trámite
            $table->string('tipo_subsidio'); 
            
            // Checkboxes de documentación (JSON)
            $table->json('docs_adjuntos')->nullable();

            // Estado y Gestión
            $table->string('estado')->default('en_revision'); 
            
            // Campos de Rechazo / Admin
            $table->unsignedBigInteger('razon_generica_id')->nullable();
            $table->text('razon_personalizada')->nullable();
            $table->string('rechazo_motivo')->nullable();

            $table->timestamps();

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organismo_id')->references('id')->on('organismos');
            $table->foreign('razon_generica_id')->references('id')->on('razones_genericas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subsidios');
    }
};