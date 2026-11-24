<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear equipo
        DB::table('equipo')->insert([
            'equ_codigo'  => 'EQ-331-001',
            'equ_titulo'  => 'Sistema de Control de Inventario para el Ambulatorio El Libertador',
            'equ_id_sec'  => 1,
            'equ_id_tra'  => 3,
            'equ_status'  => true,
        ]);

        // Integrantes del equipo
        // Líder
        DB::table('equipo_integrante')->insert([
            'ein_id_equ'      => 1,
            'ein_id_usu'      => 3,
            'ein_es_lider'    => true,
            'ein_fecha_ingreso' => now()->toDateString(),
            'ein_status'      => true,
        ]);

        // Resto del equipo
        $integrantes = [4, 5, 6, 7];
        foreach ($integrantes as $id) {
            DB::table('equipo_integrante')->insert([
                'ein_id_equ'      => 1,
                'ein_id_usu'      => $id,
                'ein_es_lider'    => false,
                'ein_fecha_ingreso' => now()->toDateString(),
                'ein_status'      => true,
            ]);
        }

        // Proyecto comunidad
        DB::table('proyecto_comunidad')->insert([
            'pco_id_equ'      => 1,
            'pco_id_com'      => 3,
            'pco_id_tpr'      => 3,
            'pco_id_mpr'      => 1,
            'pco_resumen'     => 'Sistema web para control de inventario de medicamentos y equipos médicos del ambulatorio.',
            'pco_fecha_limite' => '2026-12-15',
            'pco_status'      => true,
        ]);
    }
}