<?php

namespace App\Http\Controllers;

use App\Models\Organismo;

class OrganismoController extends Controller
{
    /**
     * Listar todos los organismos
     */
    public function index()
    {
        return Organismo::orderBy('nombre')->get();
    }
}
