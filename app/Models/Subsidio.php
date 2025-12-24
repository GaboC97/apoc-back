<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsidio extends Model
{
    use HasFactory;

protected $fillable = [
        'user_id',
        'correo_electronico',
        'apellido_nombre',
        'dni',
        'telefono',
        'organismo_id',
        'cbu_alias',
        'tipo_subsidio',
        'docs_adjuntos',
        'estado',
        'razon_generica_id',
        'razon_personalizada',
        'rechazo_motivo'
    ];

    /**
     * Casts: Convierte tipos de datos automáticamente
     * Clave para que docs_adjuntos se guarde como JSON y se lea como Array
     */
    protected $casts = [
        'docs_adjuntos' => 'array', 
    ];

    /**
     * Relaciones
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organismo()
    {
        return $this->belongsTo(Organismo::class);
    }

    public function razonGenerica()
    {
        return $this->belongsTo(RazonGenerica::class);
    }

    public function archivos()
    {
        return $this->morphMany(Archivo::class, 'archivable');
    }
}