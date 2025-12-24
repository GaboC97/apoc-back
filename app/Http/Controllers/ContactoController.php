<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoMailable; 
use App\Http\Requests\ContactRequest; // Usamos el Request de validación

class ContactoController extends Controller
{
    /**
     * Recibe la solicitud del formulario y envía el correo.
     */
    public function send(ContactRequest $request) // Usamos el ContactRequest para validación automática
    {
        // 1. La validación se maneja automáticamente por ContactRequest

        // Definir el destinatario
        $destinatario = 'gabrielcarbone97@gmail.com'; 

        try {
            // 2. Lógica de envío de correo
            Mail::to($destinatario)->send(new ContactoMailable(
                $request->nombre,
                $request->email,
                $request->mensaje
            ));
            
            return response()->json([
                'success' => true,
                'message' => '¡Mensaje enviado con éxito! Te contactaremos pronto.',
            ], 200);

        } catch (\Exception $e) {
            // 3. USAMOS NAMESPACE GLOBAL (\Log) PARA EVITAR EL ERROR DE IMPORTACIÓN DEL IDE
            \Log::error('Error al enviar correo de contacto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor al enviar el mensaje. Verifica la configuración de correo.',
            ], 500);
        }
    }
}