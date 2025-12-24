<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubsidioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'tipo_subsidio' => 'required|in:nacimiento_adopcion,hijo_menor_4,casamiento,discapacidad,derivacion_medica',
            
            // 1. VALIDACIÓN MEJORADA DE DOCUMENTOS
            // Usamos un Closure para asegurar que el array decodificado no esté vacío
            'docs_adjuntos' => [
                'required', 
                'json', 
                function ($attribute, $value, $fail) {
                    $decoded = json_decode($value, true);
                    if (!is_array($decoded) || count($decoded) < 1) {
                        $fail('Debés seleccionar al menos un documento de la lista.');
                    }
                }
            ],

            'archivo' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf',
            
            // 2. VALIDACIÓN DE CBU (Mínimo 6 caracteres para evitar errores simples)
            'cbu_alias' => 'nullable|string|min:6|max:255',
        ];

        // LOGICA ADMIN vs USUARIO
        if ($this->has('user_id') && $this->user()->role === 'admin') {
            
            $rules['user_id'] = 'required|exists:users,id';
            
            // Admin: Campos opcionales (se rellenan con datos del usuario)
            $rules['correo_electronico'] = 'nullable';
            $rules['apellido_nombre']    = 'nullable';
            $rules['dni']                = 'nullable';
            $rules['telefono']           = 'nullable';
            $rules['organismo_id']       = 'nullable'; 

        } else {
            
            // Usuario: Todo obligatorio
            $rules['correo_electronico'] = 'required|email';
            $rules['apellido_nombre']    = 'required|string|max:255';
            
            // 3. VALIDACIÓN DNI (Permitir solo números y puntos, entre 7 y 12 caracteres)
            $rules['dni']                = ['required', 'string', 'min:7', 'max:12', 'regex:/^[0-9\.]+$/']; 
            
            $rules['telefono']           = 'required|string|min:6|max:30';
            $rules['organismo_id']       = 'required|exists:organismos,id';
        }

        return $rules;
    }

    // Opcional: Mensajes personalizados para que el error sea claro en el front
    public function messages()
    {
        return [
            'docs_adjuntos.required' => 'Es necesario seleccionar la documentación a presentar.',
            'dni.regex' => 'El DNI solo debe contener números.',
            'cbu_alias.min' => 'El CBU o Alias parece demasiado corto.',
        ];
    }
}