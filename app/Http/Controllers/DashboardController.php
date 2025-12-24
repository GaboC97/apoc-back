<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reintegro;
use App\Models\Subsidio;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        // 1. Preparamos las consultas base (sin ejecutar todavía)
        // Si es Admin usa todo el modelo, si no, filtra por su ID.
        $reintegrosQuery = $isAdmin ? Reintegro::query() : Reintegro::where('user_id', $user->id);
        $subsidiosQuery  = $isAdmin ? Subsidio::query()  : Subsidio::where('user_id', $user->id);

        // 2. Función helper para sumar ambos tipos de trámite según estado
        // Usamos 'clone' para no modificar la query original y poder reutilizarla
        $contarEstado = function($estado) use ($reintegrosQuery, $subsidiosQuery) {
            return $reintegrosQuery->clone()->where('estado', $estado)->count() + 
                   $subsidiosQuery->clone()->where('estado', $estado)->count();
        };

        // 3. Calculamos los totales
        $totalReintegros = $reintegrosQuery->clone()->count();
        $totalSubsidios  = $subsidiosQuery->clone()->count();
        $totalGeneral    = $totalReintegros + $totalSubsidios;

        // 4. Devolvemos los datos
        return response()->json([
            'total'       => $totalGeneral,
            'en_revision' => $contarEstado('en_revision'),
            'aprobados'   => $contarEstado('aprobado'),
            'rechazados'  => $contarEstado('rechazado'), // <--- ¡Nuevo KPI!
        ]);
    }
}