<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // ... login y logout ...
    public function login(Request $request)
    {
        // Validación
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:4'
        ]);

        // Intento de login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ELIMINAR TOKENS ANTERIORES (Opcional, según tu lógica)
        $user->tokens()->delete();

        // Crear nuevo token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            // 👇 AQUÍ ESTABA EL PROBLEMA:
            // Antes solo devolvías id, name, email. Ahora devolvemos TODO.
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'role'         => $user->role,
                
                // Agregamos los campos que faltaban para que el Front los guarde al entrar
                'dni'          => $user->dni,
                'telefono'     => $user->telefono,
                'organismo_id' => $user->organismo_id,
                'cbu_alias'    => $user->cbu_alias,
                
                'created_at'   => $user->created_at,
            ],
        ]);
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'role'         => $user->role,
            
            // Estos ya estaban bien aquí, pero deben coincidir con login
            'dni'          => $user->dni,
            'telefono'     => $user->telefono,
            'organismo_id' => $user->organismo_id,
            'cbu_alias'    => $user->cbu_alias,
            
            'created_at'   => $user->created_at,
        ]);
    }
}