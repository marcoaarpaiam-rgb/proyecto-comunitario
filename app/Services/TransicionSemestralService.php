<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Seccion;
use App\Models\HistorialEquipo;
use App\Models\ResultadoProyecto;
use App\Models\EnvioRepositorio;
use App\Models\EstadoProyecto;

class TransicionSemestralService
{
    /**
     * Transición de Semestre Impar a Semestre Par (Mitad de Trayecto).
     * Ej: Sección 131 -> 231, 331 -> 431, 531 -> 631, 731 -> 831.
     * Guarda y congela el historial del Semestre 1, actualiza la sección y activa el Semestre 2.
     */
    public function transicionSemestreImparAPar(int $id_equipo, ?int $id_usuario = null): array
    {
        return DB::transaction(function () use ($id_equipo, $id_usuario) {
            $equipo = Equipo::with(['seccion.trayecto', 'seccion.turno', 'proyectoComunidad'])->findOrFail($id_equipo);
            $seccionActual = $equipo->seccion;

            if (!$seccionActual) {
                throw new \Exception("El equipo no tiene una sección asociada.");
            }

            $codigoActual = (string) $seccionActual->sec_codigo;
            // Extraer el primer dígito del código de sección
            $primerDigito = (int) substr($codigoActual, 0, 1);
            if ($primerDigito % 2 === 0) {
                throw new \Exception("El equipo ya se encuentra en un Semestre Par (Sección {$codigoActual}).");
            }

            // Validar Pre-Socialización aprobada (dictamen Apto obligatorio en Semestre Impar)
            $preSoc = DB::table('socializacion as s')
                ->join('tipo_socializacion as ts', 'ts.tso_id', '=', 's.soc_id_tso')
                ->where('s.soc_id_equ', $equipo->equ_id)
                ->where('ts.tso_orden', 1)
                ->where('s.soc_status', true)
                ->orderByDesc('s.soc_id')
                ->select('s.soc_apto', 's.soc_observaciones_no_apto')
                ->first();

            if (!$preSoc || $preSoc->soc_apto === null) {
                throw new \Exception("El equipo {$equipo->equ_codigo} debe contar con el dictamen 'Apto' en la Pre-Socialización antes de promoverse al Semestre Par.");
            }
            if (!$preSoc->soc_apto) {
                $obs = $preSoc->soc_observaciones_no_apto ? " Observaciones por subsanar: {$preSoc->soc_observaciones_no_apto}" : '';
                throw new \Exception("No se puede promover al equipo {$equipo->equ_codigo}: su dictamen en Pre-Socialización es 'No Apto'.{$obs}");
            }

            // Calcular código de sección del semestre par (+1 al primer dígito)
            // Ej: 131 -> 231, 331 -> 431, 531 -> 631, 731 -> 831
            $nuevoPrimerDigito = $primerDigito + 1;
            $nuevoCodigoSeccion = $nuevoPrimerDigito . substr($codigoActual, 1);

            // Buscar o crear la sección par correspondiente con el mismo trayecto y turno
            $seccionNueva = Seccion::firstOrCreate(
                [
                    'sec_codigo' => $nuevoCodigoSeccion,
                ],
                [
                    'sec_id_tra'         => $seccionActual->sec_id_tra,
                    'sec_semestre'       => $nuevoPrimerDigito,
                    'sec_id_tur'         => $seccionActual->sec_id_tur,
                    'sec_status'         => true,
                    'sec_id_usu_created' => $id_usuario ?? auth()->id(),
                ]
            );

            // Asociar automáticamente a los docentes de la sección impar a la nueva sección par si no están asignados
            $profesoresSeccion = DB::table('profesor_seccion')
                ->where('pse_id_sec', $seccionActual->sec_id)
                ->where('pse_status', true)
                ->get();

            foreach ($profesoresSeccion as $pse) {
                $existePse = DB::table('profesor_seccion')
                    ->where('pse_id_sec', $seccionNueva->sec_id)
                    ->where('pse_id_usu', $pse->pse_id_usu)
                    ->exists();

                if (!$existePse) {
                    DB::table('profesor_seccion')->insert([
                        'pse_id_sec'         => $seccionNueva->sec_id,
                        'pse_id_usu'         => $pse->pse_id_usu,
                        'pse_status'         => true,
                        'pse_created_at'     => now(),
                        'pse_id_usu_created' => $id_usuario ?? auth()->id(),
                    ]);
                }
            }

            // Asociar automáticamente a todos los estudiantes activos del equipo a la nueva sección en estudiante_seccion
            $integrantes = DB::table('equipo_integrante')
                ->where('ein_id_equ', $equipo->equ_id)
                ->where('ein_status', true)
                ->get();

            foreach ($integrantes as $integ) {
                // Cerrar asignación anterior
                DB::table('estudiante_seccion')
                    ->where('ese_id_usu', $integ->ein_id_usu)
                    ->where('ese_id_sec', $seccionActual->sec_id)
                    ->update([
                        'ese_fecha_fin'      => now()->toDateString(),
                        'ese_status'         => false,
                        'ese_updated_at'     => now(),
                        'ese_id_usu_updated' => $id_usuario ?? auth()->id(),
                    ]);

                // Asignar a la nueva sección
                $existeEstSec = DB::table('estudiante_seccion')
                    ->where('ese_id_usu', $integ->ein_id_usu)
                    ->where('ese_id_sec', $seccionNueva->sec_id)
                    ->first();

                if (!$existeEstSec) {
                    DB::table('estudiante_seccion')->insert([
                        'ese_id_usu'         => $integ->ein_id_usu,
                        'ese_id_sec'         => $seccionNueva->sec_id,
                        'ese_fecha_asignacion' => now()->toDateString(),
                        'ese_status'         => true,
                        'ese_created_at'     => now(),
                        'ese_updated_at'     => now(),
                        'ese_id_usu_created' => $id_usuario ?? auth()->id(),
                    ]);
                } else {
                    DB::table('estudiante_seccion')
                        ->where('ese_id', $existeEstSec->ese_id)
                        ->update([
                            'ese_status'         => true,
                            'ese_fecha_fin'      => null,
                            'ese_updated_at'     => now(),
                            'ese_id_usu_updated' => $id_usuario ?? auth()->id(),
                        ]);
                }
            }

            // Preservar y congelar historial de notas del Semestre 1 y puntos de control
            $notaS1 = 0;
            if ($equipo->proyectoComunidad) {
                $idProyecto = $equipo->proyectoComunidad->pco_id;

                $recorridoS1 = DB::table('recorrido_evaluacion_semestral')
                    ->where('id_proyecto', $idProyecto)
                    ->where('semestre', 'Semestre 1')
                    ->first();

                if ($recorridoS1) {
                    $notaS1 = (float) $recorridoS1->nota_cuantitativa_semestre;
                    // Asegurar que el Semestre 1 quede congelado con 40% de ponderación y marcado como Culminado
                    DB::table('recorrido_evaluacion_semestral')
                        ->where('id_recorrido', $recorridoS1->id_recorrido)
                        ->update([
                            'id_equipo'        => $equipo->equ_id,
                            'id_trayecto'      => $equipo->equ_id_tra,
                            'peso_en_trayecto' => 40.00,
                            'estado_semestre'  => 'Culminado',
                            'updated_at'       => now(),
                        ]);
                }

                // Activar Semestre 2 preservando el registro o creándolo si no existía
                $recorridoS2 = DB::table('recorrido_evaluacion_semestral')
                    ->where('id_proyecto', $idProyecto)
                    ->where('semestre', 'Semestre 2')
                    ->first();

                if (!$recorridoS2) {
                    DB::table('recorrido_evaluacion_semestral')->insert([
                        'id_proyecto'                      => $idProyecto,
                        'id_equipo'                        => $equipo->equ_id,
                        'id_trayecto'                      => $equipo->equ_id_tra,
                        'semestre'                         => 'Semestre 2',
                        'estado_semestre'                  => 'En Curso',
                        'nota_cuantitativa_semestre'       => 0.00,
                        'valoracion_cualitativa_semestre'  => null,
                        'created_at'                       => now(),
                        'updated_at'                       => now(),
                    ]);
                } else {
                    DB::table('recorrido_evaluacion_semestral')
                        ->where('id_recorrido', $recorridoS2->id_recorrido)
                        ->update([
                            'id_equipo'       => $equipo->equ_id,
                            'id_trayecto'     => $equipo->equ_id_tra,
                            'estado_semestre' => 'En Curso',
                            'updated_at'      => now(),
                        ]);
                }
            }

            // Conteo de puntos de control registrados en el trayecto (se conservan intactos asociados al equ_id)
            $puntosCount = DB::table('punto_control')
                ->where('puc_id_equ', $equipo->equ_id)
                ->where('puc_status', true)
                ->count();

            // Garantizar tipo de evento 'Transición Semestral' en tipo_evento_equipo
            $teeTransicion = DB::table('tipo_evento_equipo')->where('tee_nombre', 'Transición Semestral')->first();
            if (!$teeTransicion) {
                $idTee = DB::table('tipo_evento_equipo')->insertGetId([
                    'tee_nombre'         => 'Transición Semestral',
                    'tee_status'         => true,
                    'tee_created_at'     => now(),
                    'tee_updated_at'     => now(),
                    'tee_id_usu_created' => $id_usuario ?? auth()->id(),
                ]);
            } else {
                $idTee = $teeTransicion->tee_id;
            }

            // Guardar registro en historial del equipo
            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $idTee,
                'heq_motivo'         => "Promoción de Semestre Impar a Semestre Par: {$codigoActual} -> {$nuevoCodigoSeccion}. Calificación Semestre 1 consolidada: {$notaS1}/20 pts. {$puntosCount} Puntos de Control preservados íntegramente. Semestre 2 activado.",
                'heq_fecha_evento'   => now(),
                'heq_id_usu_created' => $id_usuario ?? auth()->id(),
            ]);

            // Actualizar sección del equipo
            $equipo->equ_id_sec = $seccionNueva->sec_id;
            $equipo->save();

            return [
                'exito'            => true,
                'seccion_anterior' => $codigoActual,
                'seccion_nueva'    => $nuevoCodigoSeccion,
                'mensaje'          => "Transición exitosa: El equipo avanzó a la sección {$nuevoCodigoSeccion} y se activó el Semestre 2.",
            ];
        });
    }

    /**
     * Transición de Semestre Par a Repositorio Institucional (Fin de Trayecto).
     * Cierra el trayecto y envía el proyecto al Repositorio con sus metadatos y actas.
     */
    public function transicionSemestreParARepositorio(int $id_equipo, ?int $id_usuario = null): array
    {
        return DB::transaction(function () use ($id_equipo, $id_usuario) {
            $equipo = Equipo::with(['seccion.trayecto', 'proyectoComunidad'])->findOrFail($id_equipo);
            $proyecto = $equipo->proyectoComunidad;

            if (!$proyecto) {
                throw new \Exception("El equipo no tiene un proyecto asignado.");
            }

            // Verificar si el acta final está cerrada y aprobada
            $acta = DB::table('actas_finales_trayecto')->where('id_proyecto', $proyecto->pco_id)->first();
            if (!$acta || !$acta->cerrada) {
                throw new \Exception("No se puede enviar al Repositorio: El proyecto aún no cuenta con un Acta Final cerrada.");
            }

            if ((float) $acta->calificacion_definitiva_cuantitativa < 16.00) {
                throw new \Exception("No se puede enviar al Repositorio: La calificación definitiva ({$acta->calificacion_definitiva_cuantitativa}) no alcanza la nota mínima aprobatoria (16.00 pts).");
            }

            // Buscar estado 'Aprobado' o similar en estado_proyecto
            $estadoAprobado = EstadoProyecto::where('epr_nombre', 'like', '%Aprobado%')->first()
                ?? EstadoProyecto::first();

            // Buscar o crear resultado_proyecto
            $resultado = ResultadoProyecto::firstOrCreate(
                [
                    'rpr_id_pco' => $proyecto->pco_id,
                ],
                [
                    'rpr_id_equ'         => $equipo->equ_id,
                    'rpr_id_tra'         => $equipo->equ_id_tra,
                    'rpr_id_epr'         => $estadoAprobado ? $estadoAprobado->epr_id : 1,
                    'rpr_observaciones'  => "Trayecto culminado satisfactoriamente con calificación de {$acta->calificacion_definitiva_cuantitativa} pts ({$acta->calificacion_definitiva_cualitativa}).",
                    'rpr_fecha_registro' => now()->toDateString(),
                    'rpr_status'         => true,
                    'rpr_id_usu_created' => $id_usuario ?? auth()->id(),
                ]
            );

            // Registrar en envio_repositorio
            $envio = EnvioRepositorio::updateOrCreate(
                [
                    'enr_id_equ' => $equipo->equ_id,
                ],
                [
                    'enr_id_rpr'         => $resultado->rpr_id,
                    'enr_estado_envio'   => 'Enviado',
                    'enr_fecha_envio'    => now(),
                    'enr_respuesta'      => "Archivado automáticamente en el Repositorio Institucional del PNFI con Hash SHA-256: {$acta->hash_verificacion}",
                    'enr_created_at'     => now(),
                    'enr_id_usu_created' => $id_usuario ?? auth()->id(),
                ]
            );

            // Garantizar tipo de evento 'Envío a Repositorio' en tipo_evento_equipo
            $teeRepo = DB::table('tipo_evento_equipo')->where('tee_nombre', 'Envío a Repositorio')->first();
            if (!$teeRepo) {
                $idTeeRepo = DB::table('tipo_evento_equipo')->insertGetId([
                    'tee_nombre'         => 'Envío a Repositorio',
                    'tee_status'         => true,
                    'tee_created_at'     => now(),
                    'tee_updated_at'     => now(),
                    'tee_id_usu_created' => $id_usuario ?? auth()->id(),
                ]);
            } else {
                $idTeeRepo = $teeRepo->tee_id;
            }

            // Registrar en historial del equipo
            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $idTeeRepo,
                'heq_motivo'         => "Culminación de Trayecto y Envío a Repositorio Institucional. Calificación definitiva: {$acta->calificacion_definitiva_cuantitativa}/20 pts ({$acta->calificacion_definitiva_cualitativa}). Hash de verificación oficial: {$acta->hash_verificacion}",
                'heq_fecha_evento'   => now(),
                'heq_id_usu_created' => $id_usuario ?? auth()->id(),
            ]);

            return [
                'exito'         => true,
                'id_envio'      => $envio->enr_id,
                'calificacion'  => $acta->calificacion_definitiva_cuantitativa,
                'mencion'       => $acta->calificacion_definitiva_cualitativa,
                'hash'          => $acta->hash_verificacion,
                'mensaje'       => "El proyecto y sus actas oficiales han sido enviados y archivados exitosamente en el Repositorio Institucional.",
            ];
        });
    }
}
