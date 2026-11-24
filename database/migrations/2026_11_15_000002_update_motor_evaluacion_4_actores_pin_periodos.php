<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Períodos Académicos
        if (!Schema::hasTable('periodos_academicos')) {
            Schema::create('periodos_academicos', function (Blueprint $table) {
                $table->id('id_periodo');
                $table->string('lapso_academico', 50)->comment("Ej: 2026-I, 2026-II");
                $table->enum('tipo_semestre', ['Impar', 'Par'])->default('Impar');
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Insertar período académico actual por defecto
            DB::table('periodos_academicos')->insert([
                'lapso_academico' => date('Y') . '-I',
                'tipo_semestre'   => 'Impar',
                'fecha_inicio'    => date('Y-01-15'),
                'fecha_fin'       => date('Y-06-30'),
                'activo'          => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // 2. Extender campos en proyecto_comunidad
        Schema::table('proyecto_comunidad', function (Blueprint $table) {
            if (!Schema::hasColumn('proyecto_comunidad', 'pin_defensa')) {
                $table->string('pin_defensa', 6)->nullable()->after('pco_resumen');
            }
            if (!Schema::hasColumn('proyecto_comunidad', 'pco_localidad')) {
                $table->string('pco_localidad', 255)->nullable()->after('pco_area_comunidad');
            }
            if (!Schema::hasColumn('proyecto_comunidad', 'pco_entrega_cd')) {
                $table->boolean('pco_entrega_cd')->default(false)->after('pco_entregado_a_tiempo');
            }
        });

        // 3. Extender campos en recorrido_evaluacion_semestral
        Schema::table('recorrido_evaluacion_semestral', function (Blueprint $table) {
            if (!Schema::hasColumn('recorrido_evaluacion_semestral', 'pin_defensa')) {
                $table->string('pin_defensa', 6)->nullable()->after('semestre');
            }
            if (!Schema::hasColumn('recorrido_evaluacion_semestral', 'nota_docente')) {
                $table->decimal('nota_docente', 4, 2)->nullable()->after('nota_tutor');
            }
            if (!Schema::hasColumn('recorrido_evaluacion_semestral', 'nota_estudiante')) {
                $table->decimal('nota_estudiante', 4, 2)->nullable()->after('nota_jurado');
            }
            if (!Schema::hasColumn('recorrido_evaluacion_semestral', 'id_periodo')) {
                $table->unsignedBigInteger('id_periodo')->nullable()->after('id_trayecto');
            }
        });

        // 4. Modificar ENUMs en rubricas y evaluaciones_emitidas para soportar los 4 actores
        DB::statement("ALTER TABLE rubricas MODIFY COLUMN rol_evaluador ENUM('docente_proyecto', 'tutor', 'comunidad', 'jurado', 'estudiante') NOT NULL");
        DB::statement("ALTER TABLE evaluaciones_emitidas MODIFY COLUMN rol_evaluador ENUM('docente_proyecto', 'tutor', 'comunidad', 'jurado', 'estudiante') NOT NULL");

        // 5. Extender evaluaciones_emitidas con id_estudiante
        Schema::table('evaluaciones_emitidas', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluaciones_emitidas', 'id_estudiante')) {
                $table->unsignedBigInteger('id_estudiante')->nullable()->after('id_profesor');
                $table->foreign('id_estudiante')->references('usu_id')->on('usuario')->onDelete('set null');
            }
        });

        // 6. Extender actas_finales_trayecto con los 4 actores oficiales
        Schema::table('actas_finales_trayecto', function (Blueprint $table) {
            if (!Schema::hasColumn('actas_finales_trayecto', 'nota_docente')) {
                $table->decimal('nota_docente', 4, 2)->nullable()->after('nota_semestre_2');
            }
            if (!Schema::hasColumn('actas_finales_trayecto', 'nota_jurado')) {
                $table->decimal('nota_jurado', 4, 2)->nullable()->after('nota_docente');
            }
            if (!Schema::hasColumn('actas_finales_trayecto', 'nota_comunidad')) {
                $table->decimal('nota_comunidad', 4, 2)->nullable()->after('nota_jurado');
            }
            if (!Schema::hasColumn('actas_finales_trayecto', 'nota_estudiante')) {
                $table->decimal('nota_estudiante', 4, 2)->nullable()->after('nota_comunidad');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas_finales_trayecto', function (Blueprint $table) {
            $table->dropColumn(['nota_docente', 'nota_jurado', 'nota_comunidad', 'nota_estudiante']);
        });

        Schema::table('evaluaciones_emitidas', function (Blueprint $table) {
            $table->dropForeign(['id_estudiante']);
            $table->dropColumn(['id_estudiante']);
        });

        Schema::table('recorrido_evaluacion_semestral', function (Blueprint $table) {
            $table->dropColumn(['pin_defensa', 'nota_docente', 'nota_estudiante', 'id_periodo']);
        });

        Schema::table('proyecto_comunidad', function (Blueprint $table) {
            $table->dropColumn(['pin_defensa', 'pco_localidad', 'pco_entrega_cd']);
        });

        Schema::dropIfExists('periodos_academicos');
    }
};
