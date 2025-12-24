<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Campos asignables en masa
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // Campos de afiliado
        'dni',
        'telefono',
        'organismo_id',
        'fecha_afiliacion',
        'estado_afiliacion',
        'cbu_alias',

        // Rol del sistema
        'role',
    ];

    /**
     * Campos que deben ocultarse en JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting automático de campos
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_afiliacion' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Relaciones
     */
    public function reintegros()
    {
        return $this->hasMany(Reintegro::class);
    }

    public function subsidios()
    {
        return $this->hasMany(Subsidio::class);
    }
    
    public function organismo()
    {
        return $this->belongsTo(Organismo::class);
    }

    /**
     * Verifica si el usuario cumplió los 6 meses de carencia
     */
    public function tieneCarenciaCumplida(): bool
    {
        if (!$this->fecha_afiliacion) {
            return false;
        }

        return now()->diffInMonths($this->fecha_afiliacion) >= 6;
    }

    /**
     * Helper: ¿Es admin?
     */
    public function esAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
