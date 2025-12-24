<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectSubsidioRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'razon_generica_id'   => 'nullable|exists:razones_genericas,id',
            'razon_personalizada' => 'nullable|string|max:500',
        ];
    }
}
