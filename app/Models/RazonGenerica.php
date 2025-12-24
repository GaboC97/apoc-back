<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RazonGenerica extends Model
{

    protected $table = 'razones_genericas';

    protected $fillable = [
        'titulo',
        'descripcion',   // opcional
        'tipo',          // reintegro o subsidio
        'activo',        // true/false
    ];

    /**
     * Relaciones
     */
    public function reintegros()
    {
        return $this->hasMany(Reintegro::class, 'razon_generica_id');
    }

    public function subsidios()
    {
        return $this->hasMany(Subsidio::class, 'razon_generica_id');
    }
}
