<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apelacion extends Model
{
    protected $fillable = [
        'tipo',             // reintegro | subsidio
        'reintegro_id',
        'subsidio_id',
        'user_id',
        'motivo',
        'archivo_path',
        'estado',           // pendiente | aprobada | rechazada
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    /** RELACIONES **/

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reintegro()
    {
        return $this->belongsTo(Reintegro::class, 'reintegro_id');
    }

    public function subsidio()
    {
        return $this->belongsTo(Subsidio::class, 'subsidio_id');
    }
}
