<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. tipo_organizacion
        $otroTor = DB::table('tipo_organizacion')
            ->whereRaw("LOWER(TRIM(tor_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('tor_id');
        if ($otroTor->isNotEmpty()) {
            $defaultTor = DB::table('tipo_organizacion')
                ->whereRaw("LOWER(TRIM(tor_nombre)) NOT IN ('otro', 'otros', 'otra', 'otras')")
                ->value('tor_id');
            if ($defaultTor) {
                DB::table('comunidad')->whereIn('com_id_tor', $otroTor)->update(['com_id_tor' => $defaultTor]);
            }
            DB::table('tipo_organizacion')->whereIn('tor_id', $otroTor)->delete();
        }

        // 2. tipo_proyecto
        $otroTpr = DB::table('tipo_proyecto')
            ->whereRaw("LOWER(TRIM(tpr_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('tpr_id');
        if ($otroTpr->isNotEmpty()) {
            $defaultTpr = DB::table('tipo_proyecto')
                ->whereRaw("LOWER(TRIM(tpr_nombre)) NOT IN ('otro', 'otros', 'otra', 'otras')")
                ->value('tpr_id');
            if ($defaultTpr) {
                DB::table('proyecto_comunidad')->whereIn('pco_id_tpr', $otroTpr)->update(['pco_id_tpr' => $defaultTpr]);
            }
            DB::table('tipo_proyecto')->whereIn('tpr_id', $otroTpr)->delete();
        }

        // 3. modalidad_proyecto
        $otroMpr = DB::table('modalidad_proyecto')
            ->whereRaw("LOWER(TRIM(mpr_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('mpr_id');
        if ($otroMpr->isNotEmpty()) {
            $defaultMpr = DB::table('modalidad_proyecto')
                ->whereRaw("LOWER(TRIM(mpr_nombre)) NOT IN ('otro', 'otros', 'otra', 'otras')")
                ->value('mpr_id');
            if ($defaultMpr) {
                DB::table('proyecto_comunidad')->whereIn('pco_id_mpr', $otroMpr)->update(['pco_id_mpr' => $defaultMpr]);
            }
            DB::table('modalidad_proyecto')->whereIn('mpr_id', $otroMpr)->delete();
        }

        // 4. tipo_beneficiario
        $otroTbe = DB::table('tipo_beneficiario')
            ->whereRaw("LOWER(TRIM(tbe_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('tbe_id');
        if ($otroTbe->isNotEmpty()) {
            $defaultTbe = DB::table('tipo_beneficiario')
                ->whereRaw("LOWER(TRIM(tbe_nombre)) NOT IN ('otro', 'otros', 'otra', 'otras')")
                ->value('tbe_id');
            if ($defaultTbe) {
                DB::table('proyecto_beneficiario')->whereIn('pbe_id_tbe', $otroTbe)->update(['pbe_id_tbe' => $defaultTbe]);
            }
            DB::table('tipo_beneficiario')->whereIn('tbe_id', $otroTbe)->delete();
        }

        // 5. tipo_jurado
        $otroTju = DB::table('tipo_jurado')
            ->whereRaw("LOWER(TRIM(tju_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('tju_id');
        if ($otroTju->isNotEmpty()) {
            $defaultTju = DB::table('tipo_jurado')
                ->whereRaw("LOWER(TRIM(tju_nombre)) NOT IN ('otro', 'otros', 'otra', 'otras')")
                ->value('tju_id');
            if ($defaultTju) {
                DB::table('punto_control_jurado')->whereIn('pcj_id_tju', $otroTju)->update(['pcj_id_tju' => $defaultTju]);
                DB::table('jurado_socializacion')->whereIn('jso_id_tju', $otroTju)->update(['jso_id_tju' => $defaultTju]);
            }
            DB::table('tipo_jurado')->whereIn('tju_id', $otroTju)->delete();
        }

        // 6. tipo_entregable_trayecto
        $otroTet = DB::table('tipo_entregable_trayecto')
            ->whereRaw("LOWER(TRIM(tet_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('tet_id');
        if ($otroTet->isNotEmpty()) {
            $defaultTet = DB::table('tipo_entregable_trayecto')
                ->whereRaw("LOWER(TRIM(tet_nombre)) NOT IN ('otro', 'otros', 'otra', 'otras')")
                ->value('tet_id');
            if ($defaultTet) {
                DB::table('entregable')->whereIn('entr_id_tet', $otroTet)->update(['entr_id_tet' => $defaultTet]);
            }
            DB::table('tipo_entregable_trayecto')->whereIn('tet_id', $otroTet)->delete();
        }

        // 7. tipo_evento_equipo
        $otroTee = DB::table('tipo_evento_equipo')
            ->whereRaw("LOWER(TRIM(tee_nombre)) IN ('otro', 'otros', 'otra', 'otras')")
            ->pluck('tee_id');
        if ($otroTee->isNotEmpty()) {
            DB::table('tipo_evento_equipo')->whereIn('tee_id', $otroTee)->delete();
        }
    }

    public function down(): void
    {
        // No recrear opciones genéricas
    }
};
