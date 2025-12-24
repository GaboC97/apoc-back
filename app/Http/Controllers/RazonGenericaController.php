<?php

namespace App\Http\Controllers;

use App\Models\RazonGenerica;

class RazonGenericaController extends Controller
{
    public function reintegros()
    {
        return RazonGenerica::where('tipo', 'reintegro')
            ->where('activo', true)
            ->orderBy('titulo')
            ->get();
    }

    public function subsidios()
    {
        return RazonGenerica::where('tipo', 'subsidio')
            ->where('activo', true)
            ->orderBy('titulo')
            ->get();
    }
}
