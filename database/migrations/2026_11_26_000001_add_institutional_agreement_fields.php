<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega columnas para acuerdos institucionales sin alterar datos previos.
     */
    public function up(): void
    {
        // 1. Equipo: bandera de excepción de cupo por desvinculación/baja justificada
        if (Schema::hasTable('equipo')) {
            Schema::table('equipo', function (Blueprint $table) {
                if (!Schema::hasColumn('equipo', 'equ_justificacion_cupo')) {
                    $table->boolean('equ_justificacion_cupo')->default(false)->after('equ_justificacion_duplicidad');
                }
                if (!Schema::hasColumn('equipo', 'equ_justificacion_cupo_motivo')) {
                    $table->text('equ_justificacion_cupo_motivo')->nullable()->after('equ_justificacion_cupo');
                }
            });
        }

        // 2. EquipoIntegrante: motivo de baja / desvinculación
        if (Schema::hasTable('equipo_integrante')) {
            Schema::table('equipo_integrante', function (Blueprint $table) {
                if (!Schema::hasColumn('equipo_integrante', 'ein_motivo_baja')) {
                    $table->text('ein_motivo_baja')->nullable()->after('ein_fecha_salida');
                }
            });
        }

        // 3. ProyectoComunidad: bandera de aprobación comunitaria y columnas nullable para etapa 1 liviana
        if (Schema::hasTable('proyecto_comunidad')) {
            Schema::table('proyecto_comunidad', function (Blueprint $table) {
                if (!Schema::hasColumn('proyecto_comunidad', 'pco_comunidad_aprobada')) {
                    $table->boolean('pco_comunidad_aprobada')->default(false)->after('pco_fecha_aprobacion');
                }
                if (!Schema::hasColumn('proyecto_comunidad', 'pco_estado_postulacion')) {
                    $table->string('pco_estado_postulacion', 40)->nullable()->after('pco_comunidad_aprobada');
                }
            });

            // Permitir que un equipo se cree sin comunidad en Etapa 1 (campos nullable en MySQL)
            try {
                DB::statement('ALTER TABLE proyecto_comunidad MODIFY pco_resumen VARCHAR(255) NULL');
                DB::statement('ALTER TABLE proyecto_comunidad MODIFY pco_fecha_limite DATE NULL');
                DB::statement('ALTER TABLE proyecto_comunidad MODIFY pco_id_com BIGINT(20) UNSIGNED NULL');
                DB::statement('ALTER TABLE proyecto_comunidad MODIFY pco_id_tpr BIGINT(20) UNSIGNED NULL');
                DB::statement('ALTER TABLE proyecto_comunidad MODIFY pco_id_mpr BIGINT(20) UNSIGNED NULL');
            } catch (\Throwable $e) {
                // Ignorar si ya son nullable o motor distinto
            }

            // Preservar proyectos previos ya aprobados
            DB::table('proyecto_comunidad')
                ->whereNotNull('pco_fecha_aprobacion')
                ->update([
                    'pco_comunidad_aprobada' => true,
                    'pco_estado_postulacion' => 'aprobada',
                ]);
        }

        // 4. SeguimientoEquipo: campos para evaluación cualitativa con baremo literal
        if (Schema::hasTable('seguimiento_equipo')) {
            Schema::table('seguimiento_equipo', function (Blueprint $table) {
                if (!Schema::hasColumn('seguimiento_equipo', 'seq_valoracion_literal')) {
                    $table->string('seq_valoracion_literal', 10)->nullable()->after('seq_valoracion_avance');
                }
                if (!Schema::hasColumn('seguimiento_equipo', 'seq_nivel_logro')) {
                    $table->string('seq_nivel_logro', 100)->nullable()->after('seq_valoracion_literal');
                }
                if (!Schema::hasColumn('seguimiento_equipo', 'seq_equivalencia_referencial')) {
                    $table->string('seq_equivalencia_referencial', 50)->nullable()->after('seq_nivel_logro');
                }
                if (!Schema::hasColumn('seguimiento_equipo', 'seq_aprobado_cualitativo')) {
                    $table->boolean('seq_aprobado_cualitativo')->nullable()->after('seq_equivalencia_referencial');
                }
            });
        }

        // 5. AsistenciaPuntoControl: valoración literal individual en semestre impar
        if (Schema::hasTable('asistencia_punto_control')) {
            Schema::table('asistencia_punto_control', function (Blueprint $table) {
                if (!Schema::hasColumn('asistencia_punto_control', 'apc_valoracion_literal')) {
                    $table->string('apc_valoracion_literal', 10)->nullable()->after('apc_participo');
                }
                if (!Schema::hasColumn('asistencia_punto_control', 'apc_nivel_logro')) {
                    $table->string('apc_nivel_logro', 100)->nullable()->after('apc_valoracion_literal');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asistencia_punto_control')) {
            Schema::table('asistencia_punto_control', function (Blueprint $table) {
                foreach (['apc_nivel_logro', 'apc_valoracion_literal'] as $col) {
                    if (Schema::hasColumn('asistencia_punto_control', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('seguimiento_equipo')) {
            Schema::table('seguimiento_equipo', function (Blueprint $table) {
                foreach (['seq_aprobado_cualitativo', 'seq_equivalencia_referencial', 'seq_nivel_logro', 'seq_valoracion_literal'] as $col) {
                    if (Schema::hasColumn('seguimiento_equipo', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('proyecto_comunidad')) {
            Schema::table('proyecto_comunidad', function (Blueprint $table) {
                foreach (['pco_estado_postulacion', 'pco_comunidad_aprobada'] as $col) {
                    if (Schema::hasColumn('proyecto_comunidad', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('equipo_integrante')) {
            Schema::table('equipo_integrante', function (Blueprint $table) {
                if (Schema::hasColumn('equipo_integrante', 'ein_motivo_baja')) {
                    $table->dropColumn('ein_motivo_baja');
                }
            });
        }

        if (Schema::hasTable('equipo')) {
            Schema::table('equipo', function (Blueprint $table) {
                foreach (['equ_justificacion_cupo_motivo', 'equ_justificacion_cupo'] as $col) {
                    if (Schema::hasColumn('equipo', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
