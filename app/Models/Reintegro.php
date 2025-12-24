<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reintegro extends Model
{
protected $fillable = [
        'user_id',
        'correo_electronico',
        'apellido_nombre',
        'dni',
        'organismo_id',
        'telefono',
        'cbu_alias',
        'fecha_factura',
        'tipo_reintegro',
        'estado',
        'razon_generica_id',
        'razon_personalizada',
        'rechazo_motivo',
    ];


    /**
     * Relaciones
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function razonGenerica()
    {
        return $this->belongsTo(RazonGenerica::class, 'razon_generica_id');
    }

    public function organismo()
    {
        return $this->belongsTo(Organismo::class);
    }

    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}
