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
        // 1. Rúbricas de evaluación por Trayecto, Semestre y Rol Evaluador
        Schema::create('rubricas', function (Blueprint $table) {
            $table->id('id_rubrica');
            $table->unsignedBigInteger('id_trayecto');
            $table->enum('semestre', ['Semestre 1', 'Semestre 2']);
            $table->enum('rol_evaluador', ['tutor', 'comunidad', 'jurado']);
            $table->string('titulo', 150);
            $table->decimal('peso_porcentual', 5, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_trayecto')->references('tra_id')->on('trayecto');
        });

        // 2. Criterios individuales de cada rúbrica (cuantitativos y cualitativos)
        Schema::create('criterios_rubrica', function (Blueprint $table) {
            $table->id('id_criterio');
            $table->unsignedBigInteger('id_rubrica');
            $table->string('nombre_criterio', 255);
            $table->enum('tipo_criterio', ['cuantitativo', 'cualitativo'])->default('cuantitativo');
            $table->decimal('puntaje_maximo', 4, 2)->default(0);
            $table->integer('orden')->default(1);

            $table->foreign('id_rubrica')->references('id_rubrica')->on('rubricas')->onDelete('cascade');
        });

        // 3. Recorrido de evaluación semestral por proyecto y equipo (Semestre 1 y Semestre 2)
        Schema::create('recorrido_evaluacion_semestral', function (Blueprint $table) {
            $table->id('id_recorrido');
            $table->unsignedBigInteger('id_proyecto');
            $table->unsignedBigInteger('id_equipo');
            $table->unsignedBigInteger('id_trayecto');
            $table->enum('semestre', ['Semestre 1', 'Semestre 2']);
            $table->decimal('peso_en_trayecto', 5, 2)->default(40.00); // 40.00 en Semestre 1, 60.00 en Semestre 2
            $table->decimal('nota_tutor', 4, 2)->nullable();
            $table->decimal('nota_comunidad', 4, 2)->nullable();
            $table->decimal('nota_jurado', 4, 2)->nullable();
            $table->decimal('nota_cuantitativa_semestre', 4, 2)->default(0);
            $table->text('valoracion_cualitativa_semestre')->nullable();
            $table->enum('estado_semestre', ['En Curso', 'En Evaluacion', 'Culminado'])->default('En Curso');
            $table->timestamps();

            $table->foreign('id_proyecto')->references('pco_id')->on('proyecto_comunidad')->onDelete('cascade');
            $table->foreign('id_equipo')->references('equ_id')->on('equipo')->onDelete('cascade');
            $table->foreign('id_trayecto')->references('tra_id')->on('trayecto');
        });

        // 4. Evaluaciones emitidas por cada evaluador (tutor, comunidad o jurado)
        Schema::create('evaluaciones_emitidas', function (Blueprint $table) {
            $table->id('id_evaluacion');
            $table->unsignedBigInteger('id_recorrido');
            $table->unsignedBigInteger('id_proyecto');
            $table->unsignedBigInteger('id_profesor')->nullable();
            $table->enum('rol_evaluador', ['tutor', 'comunidad', 'jurado']);
            $table->decimal('subtotal_cuantitativo', 4, 2)->default(0);
            $table->text('observacion_cualitativa');
            $table->enum('nivel_logro_cualitativo', [
                'Logrado con Excelencia',
                'Logrado',
                'En Proceso',
                'No Logrado'
            ]);
            $table->enum('estado', ['borrador', 'consolidado'])->default('borrador');
            $table->timestamps();

            $table->foreign('id_recorrido')->references('id_recorrido')->on('recorrido_evaluacion_semestral')->onDelete('cascade');
            $table->foreign('id_proyecto')->references('pco_id')->on('proyecto_comunidad')->onDelete('cascade');
            $table->foreign('id_profesor')->references('usu_id')->on('usuario')->onDelete('set null');
        });

        // 5. Detalles de calificación por criterio evaluado
        Schema::create('evaluacion_detalles', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('id_evaluacion');
            $table->unsignedBigInteger('id_criterio');
            $table->decimal('calificacion_obtenida', 4, 2);

            $table->foreign('id_evaluacion')->references('id_evaluacion')->on('evaluaciones_emitidas')->onDelete('cascade');
            $table->foreign('id_criterio')->references('id_criterio')->on('criterios_rubrica')->onDelete('cascade');
        });

        // 6. Actas finales de evaluación del Trayecto
        Schema::create('actas_finales_trayecto', function (Blueprint $table) {
            $table->id('id_acta');
            $table->unsignedBigInteger('id_proyecto')->unique();
            $table->unsignedBigInteger('id_equipo');
            $table->decimal('nota_semestre_1', 4, 2)->default(0);
            $table->decimal('nota_semestre_2', 4, 2)->default(0);
            $table->decimal('calificacion_definitiva_cuantitativa', 4, 2)->default(0);
            $table->enum('calificacion_definitiva_cualitativa', [
                'Reprobado',
                'Aprobado',
                'Aprobado con Mención Publicación',
                'Aprobado con Mención Honorífica'
            ])->default('Aprobado');
            $table->text('sintesis_cualitativa_integral')->nullable();
            $table->string('hash_verificacion', 64)->unique();
            $table->boolean('cerrada')->default(false);
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();

            $table->foreign('id_proyecto')->references('pco_id')->on('proyecto_comunidad')->onDelete('cascade');
            $table->foreign('id_equipo')->references('equ_id')->on('equipo');
        });

        // Vistas SQL de compatibilidad para consultas directas con alias estándar
        DB::statement("
            CREATE OR REPLACE VIEW proyectos AS 
            SELECT 
                pco_id AS id_proyecto,
                pco_id AS pco_id,
                pco_id_equ AS id_equipo,
                pco_id_equ AS pco_id_equ,
                pco_id_com AS id_comunidad,
                pco_resumen AS resumen,
                pco_fecha_limite AS fecha_limite,
                pco_fecha_aprobacion AS fecha_aprobacion,
                pco_status AS status,
                pco_created_at AS created_at,
                pco_updated_at AS updated_at
            FROM proyecto_comunidad
        ");

        DB::statement("
            CREATE OR REPLACE VIEW equipos AS 
            SELECT 
                equ_id AS id_equipo,
                equ_id AS equ_id,
                equ_codigo AS codigo,
                equ_titulo AS titulo,
                equ_id_tra AS id_trayecto,
                equ_id_sec AS id_seccion,
                equ_status AS status,
                equ_created_at AS created_at,
                equ_updated_at AS updated_at
            FROM equipo
        ");

        DB::statement("
            CREATE OR REPLACE VIEW trayectos AS 
            SELECT 
                tra_id AS id_trayecto,
                tra_id AS tra_id,
                tra_numero AS numero,
                tra_nombre AS nombre,
                tra_status AS status
            FROM trayecto
        ");

        DB::statement("
            CREATE OR REPLACE VIEW profesores AS 
            SELECT 
                usu_id AS id_profesor,
                usu_id AS usu_id,
                usu_cedula AS cedula,
                usu_primer_nombre AS primer_nombre,
                usu_primer_apellido AS primer_apellido,
                usu_email AS email,
                usu_telefono AS telefono,
                usu_status AS status
            FROM usuario
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS profesores");
        DB::statement("DROP VIEW IF EXISTS trayectos");
        DB::statement("DROP VIEW IF EXISTS equipos");
        DB::statement("DROP VIEW IF EXISTS proyectos");

        Schema::dropIfExists('actas_finales_trayecto');
        Schema::dropIfExists('evaluacion_detalles');
        Schema::dropIfExists('evaluaciones_emitidas');
        Schema::dropIfExists('recorrido_evaluacion_semestral');
        Schema::dropIfExists('criterios_rubrica');
        Schema::dropIfExists('rubricas');
    }
};
