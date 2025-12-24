<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Usuario no autenticado
        if (!$request->user()) {
            return response()->json([
                'message' => 'No autenticado'
            ], 401);
        }

        // Roles permitidos (si mañana agregás más, solo los sumás acá)
        $allowedRoles = ['admin'];

        // Usuario sin rol o rol inválido
        if (!in_array($request->user()->role, $allowedRoles)) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }

        return $next($request);
    }
}
