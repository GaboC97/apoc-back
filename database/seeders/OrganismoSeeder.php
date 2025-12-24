<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganismoSeeder extends Seeder
{
    public function run(): void
    {
        $organismos = [
            'Asesoría General de Gobierno',
            'Contaduría General de la Provincia',
            'Dirección General de Rentas',
            'Dirección General de Sumarios',
            'Ente Regulador de Servicios Públicos',
            'Fiscalía de Estado',
            'Inspección General de Justicia',
            'Instituto Provincial de la Vivienda y Desarrollo Humano',
            'Oficina Anticorrupción',
            'Registro de la Propiedad Inmueble',
            'Secretaría de Ambiente y Control del Desarrollo Sustentable',
            'Tribunal de Cuentas de la Provincia',
            'Tribunal de Cuentas Municipal de Trelew',
        ];

        foreach ($organismos as $nombre) {
            DB::table('organismos')->insert([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
