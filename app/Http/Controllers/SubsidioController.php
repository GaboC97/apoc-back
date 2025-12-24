<?php

namespace App\Http\Controllers;

use App\Models\Subsidio;
use App\Models\RazonGenerica;
use App\Models\User; // Importante: Importar el modelo User
use App\Http\Requests\StoreSubsidioRequest;
use Illuminate\Http\Request;

class SubsidioController extends Controller
{
    /**
     * Subsidios del usuario autenticado
     */
public function index(Request $request)
    {
        return Subsidio::where('user_id', $request->user()->id)
            ->with(['razonGenerica', 'organismo', 'archivos'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crear subsidio
     */
public function store(StoreSubsidioRequest $request)
    {
        // 1. **ELIMINAMOS EL MANEJO DE ARCHIVO SINGULAR** del inicio

        // 2. Determinar Usuario (Lógica Admin vs User)
        $userId = $request->user()->id;

        if ($request->user()->role === 'admin' && $request->has('user_id')) {
            $userId = $request->user_id;
        }

        $user = User::findOrFail($userId);

        // 🚨 3. VALIDACIÓN DE CARENCIA
        if (!$user->tieneCarenciaCumplida()) {
            return response()->json([
                'message' => 'No es posible solicitar el subsidio. El afiliado no cumple con los 6 meses de carencia requeridos.'
            ], 422);
        }

        // 👉 4. VALIDACIÓN DE DUPLICADOS EN REVISIÓN (Tu código de validación)
        $tipo = $request->tipo_subsidio;
        $pendiente = Subsidio::where('user_id', $user->id)
            ->where('tipo_subsidio', $tipo)
            ->where('estado', 'en_revision')
            ->first();

        if ($pendiente) {
            $nombresTipos = [
                'nacimiento_adopcion' => 'Nacimiento o Adopción',
                'hijo_menor_4'        => 'Hijo menor de 4 años',
                'casamiento'          => 'Casamiento',
                'discapacidad'        => 'Discapacidad',
                'derivacion_medica'   => 'Derivación Médica',
            ];
            $nombreTramite = $nombresTipos[$tipo] ?? 'este subsidio';
            return response()->json([
                'message' => "Ya tenés una solicitud de {$nombreTramite} en revisión. Por favor, esperá a que finalice para cargar otra."
            ], 422);
        }
        
        // 5. Lógica INTELIGENTE de CBU
        $cbuFinal = $request->cbu_alias;
        if ($cbuFinal) {
            $user->cbu_alias = $cbuFinal;
            $user->save();
        } else {
            $cbuFinal = $user->cbu_alias;
            if (!$cbuFinal) {
                return response()->json(['message' => 'El CBU es obligatorio. Por favor ingresalo manualmente.'], 422);
            }
        }

        // 6. Relleno de datos faltantes 
        $correo      = $request->correo_electronico ?? $user->email;
        $nombre      = $request->apellido_nombre ?? $user->name;
        $dni         = $request->dni ?? $user->dni;
        $telefono    = $request->telefono ?? ($user->telefono ?? 'No especificado');
        $organismoId = $request->organismo_id ?? ($user->organismo_id ?? 1);
        $docsAdjuntos = json_decode($request->docs_adjuntos, true);

        // 7. CREACIÓN DEL REGISTRO PRINCIPAL (Quitamos archivo_path y nombre_original)
        $subsidio = Subsidio::create([
            'user_id'            => $user->id,
            'correo_electronico' => $correo,
            'apellido_nombre'    => $nombre,
            'dni'                => $dni,
            'organismo_id'       => $organismoId,
            'telefono'           => $telefono,
            'cbu_alias'          => $cbuFinal,
            'tipo_subsidio'      => $request->tipo_subsidio,
            'docs_adjuntos'      => $docsAdjuntos,
            'estado'             => 'en_revision',
        ]);
        
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $path = $file->store('subsidios', 'public');
                
                $subsidio->archivos()->create([
                    'path' => $path,
                    'nombre_original' => $file->getClientOriginalName()
                ]);
            }
        } else if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $path = $file->store('subsidios', 'public');
            $subsidio->archivos()->create([
                'path' => $path,
                'nombre_original' => $file->getClientOriginalName()
            ]);
        }


        return response()->json([
            'message' => 'Subsidio enviado correctamente.',
            'data'    => $subsidio
        ], 201);
    }

    /**
     * Subsidios pendientes (ADMIN)
     */
public function all()
    {
        return Subsidio::with(['user', 'razonGenerica', 'organismo', 'archivos'])
            ->where('estado', 'en_revision')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Aprobar subsidio
     */
    public function aprobar($id)
    {
        $subsidio = Subsidio::findOrFail($id);

        $subsidio->update([
            'estado' => 'aprobado',
            'razon_generica_id' => null,
            'razon_personalizada' => null,
            'rechazo_motivo' => null,
        ]);

        return response()->json([
            'message' => 'Subsidio aprobado correctamente.',
            'subsidio' => $subsidio
        ]);
    }

    /**
     * Rechazar subsidio
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'razon_generica_id'   => 'nullable|exists:razones_genericas,id',
            'razon_personalizada' => 'nullable|string|max:500',
        ]);

        if (!$request->razon_generica_id && !$request->razon_personalizada) {
            return response()->json([
                'message' => 'Debés elegir una razón o escribir una personalizada.'
            ], 422);
        }

        $subsidio = Subsidio::findOrFail($id);

        $motivo = $request->razon_generica_id
            ? RazonGenerica::find($request->razon_generica_id)->titulo
            : $request->razon_personalizada;

        $subsidio->update([
            'estado'             => 'rechazado',
            'razon_generica_id'  => $request->razon_generica_id,
            'razon_personalizada' => $request->razon_personalizada,
            'rechazo_motivo'     => $motivo,
        ]);

        return response()->json(['message' => 'Subsidio rechazado correctamente.']);
    }

    /**
     * Historial admin
     */
public function historial()
    {
        return Subsidio::with(['user', 'razonGenerica', 'organismo', 'archivos'])
            ->orderBy('created_at', 'desc')
            ->get();
    }


public function show($id)
    {
        $subsidio = Subsidio::with('archivos')->findOrFail($id);
        $user = auth()->user();
        
        if ($subsidio->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para ver este trámite.'], 403);
        }

        return response()->json($subsidio);
    }
}
