<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Datos personales
            $table->string('dni')->nullable()->unique()->after('id');
            $table->string('telefono')->nullable()->after('dni');

            // Relación con organismos
            $table->unsignedBigInteger('organismo_id')->nullable()->after('telefono');

            // Información de afiliación
            $table->date('fecha_afiliacion')->nullable()->after('organismo_id');
            $table->enum('estado_afiliacion', [
                'activo',
                'desafiliado',
                'pendiente',
                'suspendido'
            ])->default('pendiente')->after('fecha_afiliacion');

            // CBU / Alias bancario
            $table->string('cbu_alias')->nullable()->after('estado_afiliacion');

            // Rol del sistema
            $table->string('role')->default('user')->after('password');

            // FK organismo_id → organismos.id
            $table->foreign('organismo_id')
                  ->references('id')
                  ->on('organismos')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Primero drop foreign
            $table->dropForeign(['organismo_id']);

            // Luego las columnas
            $table->dropColumn([
                'dni',
                'telefono',
                'organismo_id',
                'fecha_afiliacion',
                'estado_afiliacion',
                'cbu_alias',
                'role'
            ]);
        });
    }
};
