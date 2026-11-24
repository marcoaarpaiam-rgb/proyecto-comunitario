<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Semestre en Sección
        Schema::table('seccion', function (Blueprint $table) {
            if (!Schema::hasColumn('seccion', 'sec_semestre')) {
                $table->tinyInteger('sec_semestre')->nullable()->after('sec_id_tra')->comment('1 al 8. Impar=Cualitativo, Par=Cuantitativo');
            }
        });

        // 2. Semana y criterios en Punto de Control
        Schema::table('punto_control', function (Blueprint $table) {
            if (!Schema::hasColumn('punto_control', 'puc_semana')) {
                $table->tinyInteger('puc_semana')->default(6)->after('puc_orden')->comment('Semanas 6, 10, 14');
            }
            if (!Schema::hasColumn('punto_control', 'puc_criterios_evaluacion')) {
                $table->text('puc_criterios_evaluacion')->nullable()->after('puc_descripcion');
            }
        });

        // 3. Verificación de observaciones pasadas en Seguimiento Equipo
        Schema::table('seguimiento_equipo', function (Blueprint $table) {
            if (!Schema::hasColumn('seguimiento_equipo', 'seq_cumplio_previo')) {
                $table->boolean('seq_cumplio_previo')->nullable()->after('seq_cumplido')->comment('Cumplió con las observaciones del punto anterior');
            }
            if (!Schema::hasColumn('seguimiento_equipo', 'seq_obs_previas_verificacion')) {
                $table->text('seq_obs_previas_verificacion')->nullable()->after('seq_cumplio_previo');
            }
        });

        // 4. Justificativo en Asistencia de Punto de Control
        Schema::table('asistencia_punto_control', function (Blueprint $table) {
            if (!Schema::hasColumn('asistencia_punto_control', 'apc_justificado')) {
                $table->boolean('apc_justificado')->default(false)->after('apc_asistio');
            }
            if (!Schema::hasColumn('asistencia_punto_control', 'apc_justificativo')) {
                $table->text('apc_justificativo')->nullable()->after('apc_justificado');
            }
        });

        // 5. Asistencia en Integrante Socialización
        Schema::table('integrante_socializacion', function (Blueprint $table) {
            if (!Schema::hasColumn('integrante_socializacion', 'iso_asistio')) {
                $table->boolean('iso_asistio')->default(true)->after('iso_id_usu')->comment('Si no asiste a pre-soc reprueba');
            }
        });

        // 6. Área y Línea de investigación en Proyecto Comunidad
        Schema::table('proyecto_comunidad', function (Blueprint $table) {
            if (!Schema::hasColumn('proyecto_comunidad', 'pco_area_comunidad')) {
                $table->string('pco_area_comunidad', 150)->nullable()->after('pco_id_com')->comment('Área o departamento beneficiado para evitar duplicados');
            }
            if (!Schema::hasColumn('proyecto_comunidad', 'pco_linea_investigacion')) {
                $table->string('pco_linea_investigacion', 150)->nullable()->default('Desarrollo de Software - Web.')->after('pco_id_mpr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seccion', function (Blueprint $table) {
            $table->dropColumn(['sec_semestre']);
        });

        Schema::table('punto_control', function (Blueprint $table) {
            $table->dropColumn(['puc_semana', 'puc_criterios_evaluacion']);
        });

        Schema::table('seguimiento_equipo', function (Blueprint $table) {
            $table->dropColumn(['seq_cumplio_previo', 'seq_obs_previas_verificacion']);
        });

        Schema::table('asistencia_punto_control', function (Blueprint $table) {
            $table->dropColumn(['apc_justificado', 'apc_justificativo']);
        });

        Schema::table('integrante_socializacion', function (Blueprint $table) {
            $table->dropColumn(['iso_asistio']);
        });

        Schema::table('proyecto_comunidad', function (Blueprint $table) {
            $table->dropColumn(['pco_area_comunidad', 'pco_linea_investigacion']);
        });
    }
};
