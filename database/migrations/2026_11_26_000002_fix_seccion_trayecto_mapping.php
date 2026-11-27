<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $trayectos = DB::table('trayecto')->pluck('tra_id', 'tra_numero');

        $secciones = DB::table('seccion')->get();
        foreach ($secciones as $sec) {
            if (!preg_match('/^([1-8])/', trim((string) $sec->sec_codigo), $m)) {
                continue;
            }

            $semestre = (int) $m[1];
            $traNum = (int) ceil($semestre / 2); // 1,2->1; 3,4->2; 5,6->3; 7,8->4
            $traId = $trayectos[$traNum] ?? $sec->sec_id_tra;

            DB::table('seccion')
                ->where('sec_id', $sec->sec_id)
                ->update([
                    'sec_id_tra'   => $traId,
                    'sec_semestre' => $semestre,
                ]);

            // Sincronizar equipos existentes de la sección con el trayecto correcto
            $equipos = DB::table('equipo')->where('equ_id_sec', $sec->sec_id)->get();
            foreach ($equipos as $eq) {
                $nuevoCodigo = preg_replace(
                    '/^([A-Z]+-[A-Z]+)\d(\d)-/',
                    '${1}' . $traNum . '${2}-',
                    (string) $eq->equ_codigo
                );
                DB::table('equipo')
                    ->where('equ_id', $eq->equ_id)
                    ->update([
                        'equ_id_tra' => $traId,
                        'equ_codigo' => $nuevoCodigo ?: $eq->equ_codigo,
                    ]);
            }
        }
    }

    public function down(): void
    {
        // No revertir para preservar la integridad de secciones y equipos
    }
};
