<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RazonGenerica;

class RazonGenericaSeeder extends Seeder
{
    public function run(): void
    {
        $razones = [

            // ---------------------------------------------------
            // REINTEGROS
            // ---------------------------------------------------
            [
                'titulo' => 'Documentación ilegible',
                'tipo'   => 'reintegro',
            ],
            [
                'titulo' => 'Factura fuera de fecha permitida',
                'tipo'   => 'reintegro',
            ],
            [
                'titulo' => 'El afiliado no cumple con los 6 meses de carencia',
                'tipo'   => 'reintegro',
            ],
            [
                'titulo' => 'Falta documentación obligatoria',
                'tipo'   => 'reintegro',
            ],
            [
                'titulo' => 'Recibo no corresponde al afiliado',
                'tipo'   => 'reintegro',
            ],
            [
                'titulo' => 'No cumple los requisitos del beneficio solicitado',
                'tipo'   => 'reintegro',
            ],

            // ---------------------------------------------------
            // SUBSIDIOS
            // ---------------------------------------------------
            [
                'titulo' => 'Falta certificado requerido',
                'tipo'   => 'subsidio',
            ],
            [
                'titulo' => 'No corresponde el subsidio solicitado',
                'tipo'   => 'subsidio',
            ],
            [
                'titulo' => 'CBU / Alias inválido o no bancario',
                'tipo'   => 'subsidio',
            ],
            [
                'titulo' => 'Documentación incompleta',
                'tipo'   => 'subsidio',
            ],
            [
                'titulo' => 'La derivación no está autorizada por SEROS',
                'tipo'   => 'subsidio',
            ],
            [
                'titulo' => 'Información inconsistente en la solicitud',
                'tipo'   => 'subsidio',
            ],
        ];

        foreach ($razones as $razon) {
            RazonGenerica::create($razon);
        }
    }
}
