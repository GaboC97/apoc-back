<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    // Nombre explícito de la tabla genérica
    protected $table = 'archivos'; 

    protected $fillable = [
        'archivable_id',
        'archivable_type',
        'path',
        'nombre_original',
        // 'tipo_mime' (opcional si lo quieres llenar)
    ];
    
    /**
     * Define el lado 'hijo' de la relación polimórfica.
     * Este archivo puede pertenecer a cualquier modelo que use la relación (Reintegro, Subsidio, etc.)
     */
    public function archivable()
    {
        return $this->morphTo();
    }
}