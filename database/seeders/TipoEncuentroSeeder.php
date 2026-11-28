<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TipoEncuentroSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('tipos_encuentro_puntual')) {
            return;
        }

        $oficiales = [
            [
                'tep_nombre'      => 'Asesoría Técnica',
                'tep_descripcion' => 'Sesión de orientación técnica sobre arquitectura, código o infraestructura del proyecto.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Revisión Documental',
                'tep_descripcion' => 'Verificación de redacción, normas metodológicas e informe del proyecto sociotecnológico.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Visita Comunitaria',
                'tep_descripcion' => 'Acompañamiento o constatación de actividades ejecutadas directamente en la comunidad.',
                'tep_activo'      => true,
            ],
            [
                'tep_nombre'      => 'Seguimiento Metodológico',
                'tep_descripcion' => 'Revisión periódica de hitos, metodología y cumplimiento de objetivos del trayecto.',
                'tep_activo'      => true,
            ],
        ];

        foreach ($oficiales as $item) {
            $existe = DB::table('tipos_encuentro_puntual')
                ->whereRaw('LOWER(TRIM(tep_nombre)) = ?', [mb_strtolower(trim($item['tep_nombre']), 'UTF-8')])
                ->first();

            if ($existe) {
                DB::table('tipos_encuentro_puntual')
                    ->where('tep_id', $existe->tep_id)
                    ->update(['tep_activo' => true]);
            } else {
                DB::table('tipos_encuentro_puntual')->insert($item);
            }
        }
    }
}
