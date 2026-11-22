<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeriodoAcademicoActivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Activar lapso académico vigente y realista 2026-II (tipo Par, semana ~12 a la fecha de noviembre 2026)
            if (Schema::hasTable('periodos_academicos')) {
                // Desactivar lapsos anteriores
                DB::table('periodos_academicos')->update(['activo' => 0]);

                $lapso = '2026-II';
                $inicio = '2026-08-31'; // Lunes 31 de agosto 2026 (~12 semanas a 21 de nov 2026)
                $fin = '2027-01-15';    // Viernes 15 de enero 2027 (~6 semanas restantes)

                $existe = DB::table('periodos_academicos')
                    ->where('lapso_academico', $lapso)
                    ->first();

                if ($existe) {
                    DB::table('periodos_academicos')
                        ->where('id_periodo', $existe->id_periodo)
                        ->update([
                            'tipo_semestre' => 'Par',
                            'fecha_inicio'  => $inicio,
                            'fecha_fin'     => $fin,
                            'activo'        => 1,
                            'updated_at'    => now(),
                        ]);
                } else {
                    DB::table('periodos_academicos')->insert([
                        'lapso_academico' => $lapso,
                        'tipo_semestre'   => 'Par',
                        'fecha_inicio'    => $inicio,
                        'fecha_fin'       => $fin,
                        'activo'          => 1,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            // 2. Asignar sec_semestre coherente a todas las secciones existentes según su trayecto
            if (Schema::hasTable('seccion') && Schema::hasColumn('seccion', 'sec_semestre')) {
                // Mapear por sec_id_tra (Trayecto I -> Semestre 2, II -> 4, III -> 6, IV -> 8)
                $secciones = DB::table('seccion')->get();
                foreach ($secciones as $sec) {
                    $semestre = match ((int) ($sec->sec_id_tra ?? 1)) {
                        1 => 2,
                        2 => 4,
                        3 => 6,
                        4 => 8,
                        default => 2,
                    };

                    DB::table('seccion')
                        ->where('sec_id', $sec->sec_id)
                        ->update([
                            'sec_semestre' => $semestre,
                            'sec_updated_at' => now(),
                        ]);
                }
            }
        });
    }
}
