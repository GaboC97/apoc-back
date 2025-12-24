<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reintegros', function (Blueprint $table) {
            $table->id();

            // Usuario que lo envía
            $table->unsignedBigInteger('user_id');

            // Estado          
            $table->string('estado')->default('en_revision'); // en_revision | aprobado | rechazado

            // ——————————————————————————————
            // CAMPOS DEL FORMULARIO DE REINTEGRO
            // ——————————————————————————————
            $table->string('correo_electronico');
            $table->string('apellido_nombre');
            $table->string('dni');
            $table->unsignedBigInteger('organismo_id');
            $table->string('telefono');
            $table->string('cbu_alias');
            
            // ✅ CAMPO PARA FECHA DE LA FACTURA
            $table->date('fecha_factura')->nullable();

            // Selección única (radio)
            $table->enum('tipo_reintegro', [
                'armazon_cristales',
                'lentes_contacto',
                'actividad_fisica'
            ]);

            // ——————————————————————————————
            // MOTIVOS DE RECHAZO (SOLO ADMIN)
            // ——————————————————————————————
            $table->unsignedBigInteger('razon_generica_id')->nullable();
            $table->text('razon_personalizada')->nullable();
            
            // El motivo final que verá el usuario
            $table->string('rechazo_motivo')->nullable(); 

            $table->timestamps();

            // FK
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
                
            $table->foreign('organismo_id')
                ->references('id')->on('organismos'); 

            $table->foreign('razon_generica_id')
                ->references('id')->on('razones_genericas')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reintegros');
    }
};