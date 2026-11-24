<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposEncuentroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'tep_nombre'      => 'Revisión de Informe Técnico',
                'tep_descripcion' => 'Sesión dedicada a revisar los informes técnicos presentados por el equipo de proyecto.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Avance Técnico / Producto',
                'tep_descripcion' => 'Encuentro de seguimiento al avance técnico o del producto desarrollado por el equipo.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Seguimiento Comunitario',
                'tep_descripcion' => 'Encuentro orientado a verificar el avance de las actividades comunitarias y la interacción con la comunidad beneficiaria.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Revisión de Correcciones',
                'tep_descripcion' => 'Sesión para verificar las correcciones aplicadas a observaciones previas realizadas al equipo.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Pre-socialización / Defensa',
                'tep_descripcion' => 'Encuentro preparatorio para la socialización o defensa final del proyecto ante jurado y comunidad.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Acta de Aplazamiento',
                'tep_descripcion' => 'Registro formal de un aplazamiento acordado entre el equipo y el tutor o jurado.',
                'tep_activo'      => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_encuentro_puntual')->updateOrInsert(
                ['tep_nombre' => $tipo['tep_nombre']],
                $tipo
            );
        }
    }
}
