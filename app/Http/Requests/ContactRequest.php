<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        // La ruta de contacto es pública, por lo que permitimos el acceso.
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            'nombre'  => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'mensaje' => 'required|string|max:2000', // Límite de 2000 caracteres para el mensaje
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     */
    public function messages()
    {
        return [
            'nombre.required'  => 'Tu nombre y apellido son obligatorios.',
            'email.required'   => 'El correo electrónico es obligatorio.',
            'email.email'      => 'El formato del correo electrónico es incorrecto.',
            'mensaje.required' => 'El cuerpo del mensaje es obligatorio.',
            'mensaje.max'      => 'El mensaje no puede superar los 2000 caracteres.',
        ];
    }
}