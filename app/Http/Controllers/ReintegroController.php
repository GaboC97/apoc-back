<?php

namespace App\Http\Controllers;

use App\Models\Reintegro;
use App\Models\RazonGenerica;
use App\Http\Requests\StoreReintegroRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Archivo;

class ReintegroController extends Controller
{
    /**
     * Listar reintegros del usuario autenticado
     */
    public function index(Request $request)
    {
        return Reintegro::where('user_id', $request->user()->id)
            ->with('razonGenerica', 'organismo', 'archivos')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crear un reintegro
     */

    public function store(StoreReintegroRequest $request)
    {
        // 1. Usuario
        $userId = $request->user()->id;
        if ($request->user()->role === 'admin' && $request->has('user_id')) {
            $userId = $request->user_id;
        }
        $user = User::findOrFail($userId);

        // 2. Datos base
        $tipoReintegro = $request->tipo_reintegro;

        $fechaFactura = null;
        if ($request->filled('fecha_factura')) {
            $fechaFactura = Carbon::createFromFormat('Y-m-d', $request->fecha_factura);
        }

        // 👉 VALIDACIÓN ARMAZÓN / CRISTALES (24 meses)
        if ($tipoReintegro === 'armazon_cristales') {
            if (!$fechaFactura) {
                return response()->json(['message' => 'La fecha de la factura es obligatoria para Armazón y Cristales.'], 422);
            }

            $last = Reintegro::where('user_id', $user->id)
                ->where('tipo_reintegro', 'armazon_cristales')
                ->whereIn('estado', ['en_revision', 'aprobado'])
                ->whereNotNull('fecha_factura')
                ->orderBy('fecha_factura', 'desc')
                ->first();

            if ($last) {
                // --- CAMBIO AQUÍ: Mensaje personalizado si está en revisión ---
                if ($last->estado === 'en_revision') {
                    return response()->json([
                        'message' => 'Ya tenés una solicitud de Armazón en revisión. Por favor, aguardá a que se resuelva antes de cargar otra.'
                    ], 422);
                }
                // -------------------------------------------------------------

                $ultimaFechaFactura = Carbon::parse($last->fecha_factura);
                $fechaDisponible = $ultimaFechaFactura->copy()->addMonths(24);

                if ($fechaFactura->lt($fechaDisponible)) {
                    return response()->json([
                        'message' => 'Solo podés solicitar Armazón y Cristales cada 24 meses. Podrás volver a solicitarlo a partir del ' . $fechaDisponible->format('d/m/Y') . '.'
                    ], 422);
                }
            }
        }

        // 👉 VALIDACIÓN ACTIVIDAD FÍSICA (1 por período 15→15)
        if ($tipoReintegro === 'actividad_fisica') {
            if (!$fechaFactura) {
                return response()->json(['message' => 'La fecha de la factura es obligatoria para Actividad Física.'], 422);
            }

            $dia = $fechaFactura->day;
            if ($dia < 15) {
                $periodoInicio = $fechaFactura->copy()->subMonth()->day(15)->startOfDay();
                $periodoFin = $fechaFactura->copy()->day(15)->endOfDay();
            } else {
                $periodoInicio = $fechaFactura->copy()->day(15)->startOfDay();
                $periodoFin = $fechaFactura->copy()->addMonth()->day(15)->endOfDay();
            }

            $existe = Reintegro::where('user_id', $user->id)
                ->where('tipo_reintegro', 'actividad_fisica')
                ->whereIn('estado', ['en_revision', 'aprobado'])
                ->whereBetween('fecha_factura', [$periodoInicio, $periodoFin])
                ->first();

            if ($existe) {
                // --- CAMBIO AQUÍ: Mensaje personalizado si está en revisión ---
                if ($existe->estado === 'en_revision') {
                    return response()->json([
                        'message' => 'Ya enviaste un comprobante para este período y está en revisión. Esperá la respuesta antes de intentar nuevamente.'
                    ], 422);
                }
                // -------------------------------------------------------------

                return response()->json([
                    'message' => 'Ya existe un reintegro de Actividad Física aprobado en el período ' . $periodoInicio->format('d/m/Y') . ' al ' . $periodoFin->format('d/m/Y') . '.'
                ], 422);
            }
        }

        if ($tipoReintegro === 'lentes_contacto') {

            // A. Validar que puso fecha (siempre útil)
            if (!$fechaFactura) {
                return response()->json(['message' => 'La fecha de la factura es obligatoria.'], 422);
            }

            // B. Buscar si ya hay uno "En revisión"
            $pendiente = Reintegro::where('user_id', $user->id)
                ->where('tipo_reintegro', 'lentes_contacto')
                ->where('estado', 'en_revision')
                ->first();

            // C. Si existe, bloqueamos
            if ($pendiente) {
                return response()->json([
                    'message' => 'Ya tenés una solicitud de Lentes de Contacto en revisión. Por favor, esperá a que finalice para cargar otra.'
                ], 422);
            }
        }

        // 4. Lógica de CBU
        $cbuFinal = $request->cbu_alias;
        if ($cbuFinal) {
            $user->cbu_alias = $cbuFinal;
            $user->save();
        } else {
            $cbuFinal = $user->cbu_alias;
            if (!$cbuFinal) {
                return response()->json(['message' => 'El CBU es obligatorio.'], 422);
            }
        }

        // 5. Auto-llenado de datos
        $correo = $request->correo_electronico ?? $user->email;
        $nombre = $request->apellido_nombre ?? $user->name;
        $dni = $request->dni ?? $user->dni;
        $telefono = $request->telefono ?? ($user->telefono ?? 'No especificado');
        $organismoId = $request->organismo_id ?? ($user->organismo_id ?? 1);

        // 6. Crear
        $reintegro = Reintegro::create([
            'user_id' => $user->id,
            'correo_electronico' => $correo,
            'apellido_nombre' => $nombre,
            'dni' => $dni,
            'organismo_id' => $organismoId,
            'telefono' => $telefono,
            'cbu_alias' => $cbuFinal,
            'tipo_reintegro' => $tipoReintegro,
            'estado' => 'en_revision',
            'fecha_factura' => $fechaFactura,
        ]);

        // 2. GUARDAMOS LOS ARCHIVOS (Bucle)
        if ($request->hasFile('archivos')) { // Nota: Esperamos 'archivos' en plural
            foreach ($request->file('archivos') as $file) {

                // Guardar en disco (ej. storage/app/public/reintegros/...)
                $path = $file->store('reintegros', 'public');

                // Crear registro en la tabla genérica 'archivos' usando la relación morphMany
                $reintegro->archivos()->create([
                    'path' => $path,
                    'nombre_original' => $file->getClientOriginalName()
                ]);
            }
        }

        return response()->json(['message' => 'Enviado correctamente.'], 201);
    }

    /**
     * Reintegros pendientes (ADMIN)
     */
    public function all()
    {
        return Reintegro::with('user', 'razonGenerica', 'organismo', 'archivos')
            ->where('estado', 'en_revision')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Aprobar
     */
    public function aprobar($id)
    {
        $reintegro = Reintegro::findOrFail($id);

        $reintegro->update([
            'estado' => 'aprobado',
            'rechazo_motivo' => null,
            'razon_generica_id' => null,
            'razon_personalizada' => null,
        ]);

        return response()->json([
            'message' => 'Reintegro aprobado correctamente.',
            'reintegro' => $reintegro
        ]);
    }

    /**
     * Rechazar con motivo
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

        $reintegro = Reintegro::findOrFail($id);

        // motivo que verá el usuario
        $motivo = $request->razon_generica_id
            ? RazonGenerica::find($request->razon_generica_id)->titulo
            : $request->razon_personalizada;

        $reintegro->update([
            'estado' => 'rechazado',
            'razon_generica_id' => $request->razon_generica_id,
            'razon_personalizada' => $request->razon_personalizada,
            'rechazo_motivo' => $motivo,
        ]);

        return response()->json([
            'message' => 'Reintegro rechazado correctamente.'
        ]);
    }

    /**
     * Historial admin
     */
    public function historial()
    {
        return Reintegro::with(['user', 'razonGenerica', 'organismo', 'archivos'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show($id)
    {
        $reintegro = Reintegro::with('archivos')->findOrFail($id);
        $user = auth()->user();

        if ($reintegro->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para ver este trámite.'], 403);
        }

        return response()->json($reintegro);
    }
}
