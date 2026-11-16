<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EvaluacionTrayectoService
{
    /**
     * Inicializa los 2 semestres de recorrido de evaluación para un proyecto.
     */
    public function inicializarRecorridoProyecto(int $id_proyecto, int $id_equipo, int $id_trayecto): void
    {
        DB::transaction(function () use ($id_proyecto, $id_equipo, $id_trayecto) {
            $semestre1 = DB::table('recorrido_evaluacion_semestral')
                ->where('id_proyecto', $id_proyecto)
                ->where('semestre', 'Semestre 1')
                ->first();

            if (!$semestre1) {
                DB::table('recorrido_evaluacion_semestral')->insert([
                    'id_proyecto'       => $id_proyecto,
                    'id_equipo'         => $id_equipo,
                    'id_trayecto'       => $id_trayecto,
                    'semestre'          => 'Semestre 1',
                    'peso_en_trayecto'  => 40.00,
                    'estado_semestre'   => 'En Curso',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            $semestre2 = DB::table('recorrido_evaluacion_semestral')
                ->where('id_proyecto', $id_proyecto)
                ->where('semestre', 'Semestre 2')
                ->first();

            if (!$semestre2) {
                DB::table('recorrido_evaluacion_semestral')->insert([
                    'id_proyecto'       => $id_proyecto,
                    'id_equipo'         => $id_equipo,
                    'id_trayecto'       => $id_trayecto,
                    'semestre'          => 'Semestre 2',
                    'peso_en_trayecto'  => 60.00,
                    'estado_semestre'   => 'En Curso',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        });
    }

    /**
     * Registra o actualiza una evaluación emitida con sus detalles de rúbrica.
     */
    public function registrarEvaluacion(array $datos, array $detallesCriterios): int
    {
        return DB::transaction(function () use ($datos, $detallesCriterios) {
            // Calcular subtotal cuantitativo a partir de los criterios evaluados
            $subtotalCuantitativo = 0;
            foreach ($detallesCriterios as $id_criterio => $calificacion) {
                $subtotalCuantitativo += (float) $calificacion;
            }
            $subtotalCuantitativo = min(20.00, max(0.00, round($subtotalCuantitativo, 2)));

            // Si ya existe una evaluación previa para este recorrido, proyecto, rol y profesor (o comunidad)
            $queryExistente = DB::table('evaluaciones_emitidas')
                ->where('id_recorrido', $datos['id_recorrido'])
                ->where('id_proyecto', $datos['id_proyecto'])
                ->where('rol_evaluador', $datos['rol_evaluador']);

            if (!empty($datos['id_profesor'])) {
                $queryExistente->where('id_profesor', $datos['id_profesor']);
            }

            $evalExistente = $queryExistente->first();

            if ($evalExistente) {
                $id_evaluacion = $evalExistente->id_evaluacion;
                DB::table('evaluaciones_emitidas')
                    ->where('id_evaluacion', $id_evaluacion)
                    ->update([
                        'subtotal_cuantitativo'   => $subtotalCuantitativo,
                        'observacion_cualitativa' => $datos['observacion_cualitativa'],
                        'nivel_logro_cualitativo' => $datos['nivel_logro_cualitativo'],
                        'estado'                  => $datos['estado'] ?? 'consolidado',
                        'updated_at'              => now(),
                    ]);

                // Eliminar detalles anteriores para reinsertar actualizados
                DB::table('evaluacion_detalles')->where('id_evaluacion', $id_evaluacion)->delete();
            } else {
                $id_evaluacion = DB::table('evaluaciones_emitidas')->insertGetId([
                    'id_recorrido'            => $datos['id_recorrido'],
                    'id_proyecto'             => $datos['id_proyecto'],
                    'id_profesor'             => $datos['id_profesor'] ?? null,
                    'rol_evaluador'           => $datos['rol_evaluador'],
                    'subtotal_cuantitativo'   => $subtotalCuantitativo,
                    'observacion_cualitativa' => $datos['observacion_cualitativa'],
                    'nivel_logro_cualitativo' => $datos['nivel_logro_cualitativo'],
                    'estado'                  => $datos['estado'] ?? 'consolidado',
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);
            }

            // Insertar calificaciones por cada criterio
            foreach ($detallesCriterios as $id_criterio => $calificacion) {
                DB::table('evaluacion_detalles')->insert([
                    'id_evaluacion'         => $id_evaluacion,
                    'id_criterio'           => $id_criterio,
                    'calificacion_obtenida' => min(20.00, max(0.00, round((float) $calificacion, 2))),
                ]);
            }

            // Actualizar el estado del recorrido y ejecutar cálculo si corresponde
            $this->calcularConsolidadoSemestre((int) $datos['id_recorrido']);

            return $id_evaluacion;
        });
    }

    /**
     * Calcula la nota del semestre cuando los evaluadores requeridos han consolidado sus notas.
     */
    public function calcularConsolidadoSemestre(int $id_recorrido): ?array
    {
        return DB::transaction(function () use ($id_recorrido) {
            $recorrido = DB::table('recorrido_evaluacion_semestral')
                ->where('id_recorrido', $id_recorrido)
                ->first();

            if (!$recorrido) {
                return null;
            }

            $evaluaciones = DB::table('evaluaciones_emitidas')
                ->where('id_recorrido', $id_recorrido)
                ->where('estado', 'consolidado')
                ->get();

            $evalTutor = $evaluaciones->firstWhere('rol_evaluador', 'tutor');
            $evalComunidad = $evaluaciones->firstWhere('rol_evaluador', 'comunidad');
            $evalJurados = $evaluaciones->where('rol_evaluador', 'jurado');

            $notaTutor = $evalTutor ? (float) $evalTutor->subtotal_cuantitativo : null;
            $notaComunidad = $evalComunidad ? (float) $evalComunidad->subtotal_cuantitativo : null;
            $notaJurado = null;

            if ($evalJurados->isNotEmpty()) {
                $notaJurado = round($evalJurados->avg('subtotal_cuantitativo'), 2);
            }

            $updateData = [
                'nota_tutor'     => $notaTutor,
                'nota_comunidad' => $notaComunidad,
                'nota_jurado'    => $notaJurado,
                'updated_at'     => now(),
            ];

            $sintesis = [];
            if ($evalTutor) {
                $sintesis[] = "TUTOR ({$evalTutor->nivel_logro_cualitativo}): {$evalTutor->observacion_cualitativa}";
            }
            if ($evalComunidad) {
                $sintesis[] = "COMUNIDAD ({$evalComunidad->nivel_logro_cualitativo}): {$evalComunidad->observacion_cualitativa}";
            }
            foreach ($evalJurados as $idx => $j) {
                $numJ = $idx + 1;
                $sintesis[] = "JURADO #{$numJ} ({$j->nivel_logro_cualitativo}): {$j->observacion_cualitativa}";
            }

            // Regla de cálculo Semestre 1 (40% del Trayecto): Requiere Tutor (60%) y Comunidad (40%)
            if ($recorrido->semestre === 'Semestre 1') {
                if ($notaTutor !== null && $notaComunidad !== null) {
                    $notaSemestre1 = round(($notaTutor * 0.60) + ($notaComunidad * 0.40), 2);
                    $updateData['nota_cuantitativa_semestre'] = $notaSemestre1;
                    $updateData['valoracion_cualitativa_semestre'] = implode("\n\n", $sintesis);
                    $updateData['estado_semestre'] = 'Culminado';
                } elseif ($notaTutor !== null || $notaComunidad !== null) {
                    $updateData['estado_semestre'] = 'En Evaluacion';
                }
            }

            // Regla de cálculo Semestre 2 (60% del Trayecto): Requiere Tutor (30%), Comunidad (20%) y Jurado (50%)
            if ($recorrido->semestre === 'Semestre 2') {
                if ($notaTutor !== null && $notaComunidad !== null && $notaJurado !== null) {
                    $notaSemestre2 = round(($notaTutor * 0.30) + ($notaComunidad * 0.20) + ($notaJurado * 0.50), 2);
                    $updateData['nota_cuantitativa_semestre'] = $notaSemestre2;
                    $updateData['valoracion_cualitativa_semestre'] = implode("\n\n", $sintesis);
                    $updateData['estado_semestre'] = 'Culminado';
                } elseif ($notaTutor !== null || $notaComunidad !== null || $notaJurado !== null) {
                    $updateData['estado_semestre'] = 'En Evaluacion';
                }
            }

            DB::table('recorrido_evaluacion_semestral')
                ->where('id_recorrido', $id_recorrido)
                ->update($updateData);

            // Verificar si ambos semestres están culminados para cerrar automáticamente el trayecto
            $this->verificarCierreTrayecto((int) $recorrido->id_proyecto, (int) $recorrido->id_equipo);

            return $updateData;
        });
    }

    /**
     * Verifica si Semestre 1 y Semestre 2 están culminados para generar o actualizar el acta final.
     */
    public function verificarCierreTrayecto(int $id_proyecto, int $id_equipo): ?array
    {
        $semestre1 = DB::table('recorrido_evaluacion_semestral')
            ->where('id_proyecto', $id_proyecto)
            ->where('semestre', 'Semestre 1')
            ->first();

        $semestre2 = DB::table('recorrido_evaluacion_semestral')
            ->where('id_proyecto', $id_proyecto)
            ->where('semestre', 'Semestre 2')
            ->first();

        if ($semestre1 && $semestre2 && $semestre1->estado_semestre === 'Culminado' && $semestre2->estado_semestre === 'Culminado') {
            return $this->cerrarTrayectoYGenerarActa($id_proyecto, $id_equipo, (float) $semestre1->nota_cuantitativa_semestre, (float) $semestre2->nota_cuantitativa_semestre);
        }

        return null;
    }

    /**
     * Cierra el trayecto y genera el Acta Final oficial con veredicto cualitativo y hash SHA-256.
     */
    public function cerrarTrayectoYGenerarActa(int $id_proyecto, int $id_equipo, float $notaSemestre1, float $notaSemestre2): array
    {
        return DB::transaction(function () use ($id_proyecto, $id_equipo, $notaSemestre1, $notaSemestre2) {
            // Nota definitiva ponderada: 40% Semestre 1 + 60% Semestre 2
            $calificacionDefinitiva = round(($notaSemestre1 * 0.40) + ($notaSemestre2 * 0.60), 2);

            // Veredicto oficial cualitativo
            if ($calificacionDefinitiva >= 19.50) {
                $veredictoCualitativo = 'Aprobado con Mención Honorífica';
            } elseif ($calificacionDefinitiva >= 18.00) {
                $veredictoCualitativo = 'Aprobado con Mención Publicación';
            } elseif ($calificacionDefinitiva >= 16.00) {
                $veredictoCualitativo = 'Aprobado';
            } else {
                $veredictoCualitativo = 'Reprobado';
            }

            // Síntesis integral de ambos semestres
            $recorridos = DB::table('recorrido_evaluacion_semestral')
                ->where('id_proyecto', $id_proyecto)
                ->orderBy('semestre')
                ->get();

            $sintesis = [];
            foreach ($recorridos as $rec) {
                $sintesis[] = "=== {$rec->semestre} (Calificación: {$rec->nota_cuantitativa_semestre} pts) ===\n" . ($rec->valoracion_cualitativa_semestre ?: 'Sin observaciones.');
            }
            $sintesisIntegral = implode("\n\n", $sintesis);

            // Generar hash único de verificación
            $hashString = "UPTP-PST-ACTA-PROYECTO-{$id_proyecto}-EQUIPO-{$id_equipo}-NOTA-{$calificacionDefinitiva}-" . now()->format('YmdHis') . '-' . Str::random(12);
            $hashVerificacion = hash('sha256', $hashString);

            $actaExistente = DB::table('actas_finales_trayecto')
                ->where('id_proyecto', $id_proyecto)
                ->first();

            $actaData = [
                'id_proyecto'                         => $id_proyecto,
                'id_equipo'                           => $id_equipo,
                'nota_semestre_1'                     => $notaSemestre1,
                'nota_semestre_2'                     => $notaSemestre2,
                'calificacion_definitiva_cuantitativa' => $calificacionDefinitiva,
                'calificacion_definitiva_cualitativa' => $veredictoCualitativo,
                'sintesis_cualitativa_integral'       => $sintesisIntegral,
                'hash_verificacion'                   => $actaExistente ? $actaExistente->hash_verificacion : $hashVerificacion,
                'cerrada'                             => true,
                'fecha_cierre'                        => now(),
                'updated_at'                          => now(),
            ];

            if ($actaExistente) {
                DB::table('actas_finales_trayecto')
                    ->where('id_acta', $actaExistente->id_acta)
                    ->update($actaData);
                $id_acta = $actaExistente->id_acta;
            } else {
                $actaData['created_at'] = now();
                $id_acta = DB::table('actas_finales_trayecto')->insertGetId($actaData);
            }

            return array_merge(['id_acta' => $id_acta], $actaData);
        });
    }

    /**
     * Obtiene el recorrido completo de evaluación de un proyecto con sus datos relacionados.
     */
    public function obtenerRecorridoCompleto(int $id_proyecto): ?object
    {
        $proyecto = DB::table('proyecto_comunidad as p')
            ->join('equipo as e', 'e.equ_id', '=', 'p.pco_id_equ')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'e.equ_id_tra')
            ->leftJoin('seccion as s', 's.sec_id', '=', 'e.equ_id_sec')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->leftJoin('tutor as tut', function ($j) {
                $j->on('tut.tut_id_equ', '=', 'e.equ_id')->where('tut.tut_status', true);
            })
            ->leftJoin('usuario as u_tut', 'u_tut.usu_id', '=', 'tut.tut_id_usu')
            ->leftJoin('responsable_comunidad as rc', function ($j) {
                $j->on('rc.rco_id_com', '=', 'c.com_id')->where('rc.rco_status', true);
            })
            ->leftJoin('usuario as u_rc', 'u_rc.usu_id', '=', 'rc.rco_id_usu')
            ->where('p.pco_id', $id_proyecto)
            ->select([
                'p.pco_id as id_proyecto',
                'p.pco_resumen as resumen',
                'p.pco_linea_investigacion as linea_investigacion',
                'p.pco_fecha_aprobacion as fecha_aprobacion',
                'e.equ_id as id_equipo',
                'e.equ_codigo as codigo_equipo',
                'e.equ_titulo as titulo_proyecto',
                't.tra_id as id_trayecto',
                't.tra_nombre as trayecto_nombre',
                's.sec_codigo as seccion_codigo',
                'c.com_nombre as comunidad_nombre',
                'c.com_ubicacion as comunidad_ubicacion',
                'u_tut.usu_id as tutor_id',
                'u_tut.usu_primer_nombre as tutor_nombre',
                'u_tut.usu_primer_apellido as tutor_apellido',
                'u_tut.usu_cedula as tutor_cedula',
                'u_tut.usu_email as tutor_email',
                DB::raw("CONCAT(u_rc.usu_primer_nombre, ' ', u_rc.usu_primer_apellido) as responsable_comunitario"),
            ])
            ->first();

        if (!$proyecto) {
            return null;
        }

        // Asegurar que el recorrido esté inicializado
        $this->inicializarRecorridoProyecto((int) $proyecto->id_proyecto, (int) $proyecto->id_equipo, (int) $proyecto->id_trayecto);

        $semestres = DB::table('recorrido_evaluacion_semestral')
            ->where('id_proyecto', $id_proyecto)
            ->orderBy('semestre')
            ->get()
            ->map(function ($sem) use ($id_proyecto, $proyecto) {
                $sem->evaluaciones = DB::table('evaluaciones_emitidas as ee')
                    ->leftJoin('usuario as u', 'u.usu_id', '=', 'ee.id_profesor')
                    ->leftJoin('rubricas as rub', function ($j) use ($sem, $proyecto) {
                        $j->on('rub.id_trayecto', '=', DB::raw((int) $proyecto->id_trayecto))
                          ->on('rub.semestre', '=', DB::raw("'" . $sem->semestre . "'"))
                          ->on('rub.rol_evaluador', '=', 'ee.rol_evaluador')
                          ->where('rub.activo', true);
                    })
                    ->where('ee.id_recorrido', $sem->id_recorrido)
                    ->select([
                        'ee.*',
                        'ee.subtotal_cuantitativo as calificacion_cuantitativa',
                        'u.usu_primer_nombre as prof_nombre',
                        'u.usu_primer_apellido as prof_apellido',
                        'u.usu_cedula as prof_cedula',
                        'u.usu_primer_nombre as evaluador_nombre',
                        'u.usu_primer_apellido as evaluador_apellido',
                        DB::raw("COALESCE(rub.peso_porcentual, CASE WHEN ee.rol_evaluador = 'tutor' THEN (CASE WHEN '{$sem->semestre}' = 'Semestre 1' THEN 60.00 ELSE 30.00 END) WHEN ee.rol_evaluador = 'comunidad' THEN (CASE WHEN '{$sem->semestre}' = 'Semestre 1' THEN 40.00 ELSE 20.00 END) ELSE 50.00 END) as peso_porcentual"),
                    ])
                    ->get();

                return $sem;
            });

        $acta = DB::table('actas_finales_trayecto')
            ->where('id_proyecto', $id_proyecto)
            ->first();

        // Integrantes del equipo
        $integrantes = DB::table('equipo_integrante as ei')
            ->join('usuario as u', 'u.usu_id', '=', 'ei.ein_id_usu')
            ->where('ei.ein_id_equ', $proyecto->id_equipo)
            ->where('ei.ein_status', true)
            ->select([
                'u.usu_id as id_estudiante',
                'u.usu_cedula as cedula',
                'u.usu_cedula as usu_cedula',
                'u.usu_primer_nombre as primer_nombre',
                'u.usu_primer_nombre as usu_primer_nombre',
                'u.usu_primer_apellido as primer_apellido',
                'u.usu_primer_apellido as usu_primer_apellido',
                'ei.ein_es_lider as es_lider',
                'ei.ein_es_lider',
            ])
            ->get();

        return (object) [
            'proyecto'    => $proyecto,
            'semestres'   => $semestres,
            'acta'        => $acta,
            'integrantes' => $integrantes,
        ];
    }
}
