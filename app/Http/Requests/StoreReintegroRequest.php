<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReintegroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'tipo_reintegro' => 'required|in:armazon_cristales,lentes_contacto,actividad_fisica',
            'archivo'        => 'nullable|file|max:10240',
            'cbu_alias'      => 'nullable|string|min:6|max:255',
            // NUEVO: La fecha es obligatoria si el tipo es óptico o físico.
            'fecha_factura'  => 'nullable|date', 
        ];

        // Regla condicional para hacer la FECHA_FACTURA REQUERIDA
        // si el tipo de reintegro lo requiere.
        if (in_array($this->tipo_reintegro, ['armazon_cristales', 'lentes_contacto', 'actividad_fisica'])) {
            $rules['fecha_factura'] = 'required|date|before_or_equal:today';
        }


        // Lógica Admin vs User (Se mantiene igual)
        if ($this->has('user_id') && $this->user()->role === 'admin') {
            $rules['user_id'] = 'required|exists:users,id';
            
            $rules['correo_electronico'] = 'nullable';
            $rules['apellido_nombre']    = 'nullable';
            $rules['dni']                = 'nullable';
            $rules['telefono']           = 'nullable';
            $rules['organismo_id']       = 'nullable'; 
        } else {
            // Obligatorios para usuario
            $rules['correo_electronico'] = 'required|email';
            $rules['apellido_nombre']    = 'required|string';
            $rules['dni']                = 'required|string';
            $rules['telefono']           = 'required|string';
            $rules['organismo_id']       = 'required|exists:organismos,id';
        }

        return $rules;
    }

    public function messages()
    {
        // Agregar mensaje claro para la fecha
        return [
            'fecha_factura.required' => 'La fecha de la factura es obligatoria para este tipo de reintegro.',
            'fecha_factura.date' => 'El formato de la fecha no es válido.',
            'fecha_factura.before_or_equal' => 'La fecha de la factura no puede ser en el futuro.',
            // ... otros mensajes
        ];
    }
}