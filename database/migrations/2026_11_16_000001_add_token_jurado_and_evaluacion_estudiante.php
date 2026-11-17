<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agregar token temporal y expiración a proyecto_comunidad
        Schema::table('proyecto_comunidad', function (Blueprint $table) {
            if (!Schema::hasColumn('proyecto_comunidad', 'token_jurado')) {
                $table->string('token_jurado', 64)->nullable()->unique()->after('pin_defensa');
            }
            if (!Schema::hasColumn('proyecto_comunidad', 'token_jurado_expira_at')) {
                $table->timestamp('token_jurado_expira_at')->nullable()->after('token_jurado');
            }
        });

        // 2. Tabla para almacenar calificaciones individualizadas asignadas por jurados y voceros
        if (!Schema::hasTable('evaluaciones_jurado_estudiante')) {
            Schema::create('evaluaciones_jurado_estudiante', function (Blueprint $table) {
                $table->id('id_eval_jurado_est');
                $table->unsignedBigInteger('id_proyecto');
                $table->unsignedBigInteger('id_equipo');
                $table->unsignedBigInteger('id_recorrido')->nullable();
                $table->string('token_usado', 64)->nullable();
                $table->enum('rol_evaluador', ['jurado', 'comunidad'])->default('jurado');
                $table->string('nombre_evaluador', 150);
                $table->string('cedula_evaluador', 50);
                $table->string('institucion_evaluador', 150)->nullable();
                $table->unsignedBigInteger('id_estudiante');
                $table->decimal('calificacion', 4, 2);
                $table->text('observacion_individual')->nullable();
                $table->text('observacion_general')->nullable();
                $table->string('nivel_logro', 50)->nullable();
                $table->timestamps();

                $table->foreign('id_proyecto')->references('pco_id')->on('proyecto_comunidad')->onDelete('cascade');
                $table->foreign('id_equipo')->references('equ_id')->on('equipo')->onDelete('cascade');
                $table->foreign('id_estudiante')->references('usu_id')->on('usuario')->onDelete('cascade');
                $table->index(['id_proyecto', 'cedula_evaluador'], 'idx_eje_proy_ced');
                $table->index('token_usado', 'idx_eje_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_jurado_estudiante');

        Schema::table('proyecto_comunidad', function (Blueprint $table) {
            if (Schema::hasColumn('proyecto_comunidad', 'token_jurado_expira_at')) {
                $table->dropColumn('token_jurado_expira_at');
            }
            if (Schema::hasColumn('proyecto_comunidad', 'token_jurado')) {
                $table->dropColumn('token_jurado');
            }
        });
    }
};
