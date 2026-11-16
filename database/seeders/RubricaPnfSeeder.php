<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\EvaluacionTrayectoService;

class RubricaPnfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trayectos = DB::table('trayecto')->get();

        foreach ($trayectos as $trayecto) {
            $idTrayecto = $trayecto->tra_id;

            // ==========================================
            // SEMESTRE 1: TUTOR (60%) Y COMUNIDAD (40%)
            // ==========================================

            // 1. Rúbrica Tutor - Semestre 1 (60%)
            $rubricaTutorS1 = DB::table('rubricas')->where([
                'id_trayecto'   => $idTrayecto,
                'semestre'      => 'Semestre 1',
                'rol_evaluador' => 'tutor',
            ])->first();

            if (!$rubricaTutorS1) {
                $idRubTutorS1 = DB::table('rubricas')->insertGetId([
                    'id_trayecto'     => $idTrayecto,
                    'semestre'        => 'Semestre 1',
                    'rol_evaluador'   => 'tutor',
                    'titulo'          => "Rúbrica del Tutor Académico - Semestre 1 ({$trayecto->tra_nombre})",
                    'peso_porcentual' => 60.00,
                    'activo'          => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('criterios_rubrica')->insert([
                    [
                        'id_rubrica'      => $idRubTutorS1,
                        'nombre_criterio' => 'Asistencia, asesorías periódicas y cumplimiento del plan de trabajo',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 4.00,
                        'orden'           => 1,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS1,
                        'nombre_criterio' => 'Diagnóstico participativo, fundamentación teórica y contextualización comunitaria',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 6.00,
                        'orden'           => 2,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS1,
                        'nombre_criterio' => 'Diseño metodológico, definición de requerimientos y viabilidad de la propuesta',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 3,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS1,
                        'nombre_criterio' => 'Estructura, redacción y coherencia técnica del informe de avance del semestre 1',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 4,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS1,
                        'nombre_criterio' => 'Compromiso ético, iniciativa y trabajo en equipo del colectivo estudiantil',
                        'tipo_criterio'   => 'cualitativo',
                        'puntaje_maximo'  => 0.00,
                        'orden'           => 5,
                    ],
                ]);
            }

            // 2. Rúbrica Comunidad - Semestre 1 (40%)
            $rubricaComS1 = DB::table('rubricas')->where([
                'id_trayecto'   => $idTrayecto,
                'semestre'      => 'Semestre 1',
                'rol_evaluador' => 'comunidad',
            ])->first();

            if (!$rubricaComS1) {
                $idRubComS1 = DB::table('rubricas')->insertGetId([
                    'id_trayecto'     => $idTrayecto,
                    'semestre'        => 'Semestre 1',
                    'rol_evaluador'   => 'comunidad',
                    'titulo'          => "Rúbrica de la Comunidad - Semestre 1 ({$trayecto->tra_nombre})",
                    'peso_porcentual' => 40.00,
                    'activo'          => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('criterios_rubrica')->insert([
                    [
                        'id_rubrica'      => $idRubComS1,
                        'nombre_criterio' => 'Integración, respeto y comunicación asertiva con los voceros comunitarios',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 1,
                    ],
                    [
                        'id_rubrica'      => $idRubComS1,
                        'nombre_criterio' => 'Participación activa en mesas de trabajo y levantamiento de requerimientos en campo',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 2,
                    ],
                    [
                        'id_rubrica'      => $idRubComS1,
                        'nombre_criterio' => 'Puntualidad, asistencia y cumplimiento de las visitas pautadas a la comunidad',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 3,
                    ],
                    [
                        'id_rubrica'      => $idRubComS1,
                        'nombre_criterio' => 'Pertinencia, claridad y utilidad de la solución tecnológica preliminar planteada',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 4,
                    ],
                    [
                        'id_rubrica'      => $idRubComS1,
                        'nombre_criterio' => 'Nivel de receptividad y satisfacción general de la comunidad con el equipo',
                        'tipo_criterio'   => 'cualitativo',
                        'puntaje_maximo'  => 0.00,
                        'orden'           => 5,
                    ],
                ]);
            }

            // ========================================================
            // SEMESTRE 2: TUTOR (30%), COMUNIDAD (20%) Y JURADO (50%)
            // ========================================================

            // 3. Rúbrica Tutor - Semestre 2 (30%)
            $rubricaTutorS2 = DB::table('rubricas')->where([
                'id_trayecto'   => $idTrayecto,
                'semestre'      => 'Semestre 2',
                'rol_evaluador' => 'tutor',
            ])->first();

            if (!$rubricaTutorS2) {
                $idRubTutorS2 = DB::table('rubricas')->insertGetId([
                    'id_trayecto'     => $idTrayecto,
                    'semestre'        => 'Semestre 2',
                    'rol_evaluador'   => 'tutor',
                    'titulo'          => "Rúbrica del Tutor Académico - Semestre 2 ({$trayecto->tra_nombre})",
                    'peso_porcentual' => 30.00,
                    'activo'          => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('criterios_rubrica')->insert([
                    [
                        'id_rubrica'      => $idRubTutorS2,
                        'nombre_criterio' => 'Arquitectura de software, estándares de codificación y buenas prácticas técnicas',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 6.00,
                        'orden'           => 1,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS2,
                        'nombre_criterio' => 'Operatividad del software y cumplimiento de los requisitos del sistema',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 6.00,
                        'orden'           => 2,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS2,
                        'nombre_criterio' => 'Calidad, completitud y rigurosidad técnica del informe final y manuales',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 5.00,
                        'orden'           => 3,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS2,
                        'nombre_criterio' => 'Seguimiento continuo y superación de las observaciones del primer semestre',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 3.00,
                        'orden'           => 4,
                    ],
                    [
                        'id_rubrica'      => $idRubTutorS2,
                        'nombre_criterio' => 'Madurez investigativa y capacidad de resolución de retos tecnológicos',
                        'tipo_criterio'   => 'cualitativo',
                        'puntaje_maximo'  => 0.00,
                        'orden'           => 5,
                    ],
                ]);
            }

            // 4. Rúbrica Comunidad - Semestre 2 (20%)
            $rubricaComS2 = DB::table('rubricas')->where([
                'id_trayecto'   => $idTrayecto,
                'semestre'      => 'Semestre 2',
                'rol_evaluador' => 'comunidad',
            ])->first();

            if (!$rubricaComS2) {
                $idRubComS2 = DB::table('rubricas')->insertGetId([
                    'id_trayecto'     => $idTrayecto,
                    'semestre'        => 'Semestre 2',
                    'rol_evaluador'   => 'comunidad',
                    'titulo'          => "Rúbrica de la Comunidad - Semestre 2 ({$trayecto->tra_nombre})",
                    'peso_porcentual' => 20.00,
                    'activo'          => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('criterios_rubrica')->insert([
                    [
                        'id_rubrica'      => $idRubComS2,
                        'nombre_criterio' => 'Funcionalidad y solución real de la necesidad comunitaria a través del software',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 7.00,
                        'orden'           => 1,
                    ],
                    [
                        'id_rubrica'      => $idRubComS2,
                        'nombre_criterio' => 'Capacitación a los usuarios, transferencia tecnológica y entrega de manuales',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 7.00,
                        'orden'           => 2,
                    ],
                    [
                        'id_rubrica'      => $idRubComS2,
                        'nombre_criterio' => 'Responsabilidad, cumplimiento y entrega formal del producto en el tiempo acordado',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 6.00,
                        'orden'           => 3,
                    ],
                    [
                        'id_rubrica'      => $idRubComS2,
                        'nombre_criterio' => 'Impacto social y grado de apropiación comunitaria de la tecnología desarrollada',
                        'tipo_criterio'   => 'cualitativo',
                        'puntaje_maximo'  => 0.00,
                        'orden'           => 4,
                    ],
                ]);
            }

            // 5. Rúbrica Jurados - Semestre 2 (50%)
            $rubricaJurS2 = DB::table('rubricas')->where([
                'id_trayecto'   => $idTrayecto,
                'semestre'      => 'Semestre 2',
                'rol_evaluador' => 'jurado',
            ])->first();

            if (!$rubricaJurS2) {
                $idRubJurS2 = DB::table('rubricas')->insertGetId([
                    'id_trayecto'     => $idTrayecto,
                    'semestre'        => 'Semestre 2',
                    'rol_evaluador'   => 'jurado',
                    'titulo'          => "Rúbrica de Jurados Evaluadores - Semestre 2 ({$trayecto->tra_nombre})",
                    'peso_porcentual' => 50.00,
                    'activo'          => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('criterios_rubrica')->insert([
                    [
                        'id_rubrica'      => $idRubJurS2,
                        'nombre_criterio' => 'Dominio conceptual, coherencia discursiva y argumentación técnica en la defensa',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 6.00,
                        'orden'           => 1,
                    ],
                    [
                        'id_rubrica'      => $idRubJurS2,
                        'nombre_criterio' => 'Demostración en vivo del software (interfaz, funcionalidad, pruebas y seguridad)',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 8.00,
                        'orden'           => 2,
                    ],
                    [
                        'id_rubrica'      => $idRubJurS2,
                        'nombre_criterio' => 'Calidad, sustento metodológico y aportes del informe técnico final',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 4.00,
                        'orden'           => 3,
                    ],
                    [
                        'id_rubrica'      => $idRubJurS2,
                        'nombre_criterio' => 'Capacidad de respuesta y precisión ante las preguntas del comité evaluador',
                        'tipo_criterio'   => 'cuantitativo',
                        'puntaje_maximo'  => 2.00,
                        'orden'           => 4,
                    ],
                    [
                        'id_rubrica'      => $idRubJurS2,
                        'nombre_criterio' => 'Postura profesional, ética y solvencia investigativa del equipo',
                        'tipo_criterio'   => 'cualitativo',
                        'puntaje_maximo'  => 0.00,
                        'orden'           => 5,
                    ],
                ]);
            }
        }

        // Inicializar el recorrido semestral de los proyectos existentes en la base de datos
        $evalService = new EvaluacionTrayectoService();
        $proyectosExistentes = DB::table('proyecto_comunidad as p')
            ->join('equipo as e', 'e.equ_id', '=', 'p.pco_id_equ')
            ->select('p.pco_id', 'p.pco_id_equ', 'e.equ_id_tra')
            ->get();

        foreach ($proyectosExistentes as $p) {
            $evalService->inicializarRecorridoProyecto((int) $p->pco_id, (int) $p->pco_id_equ, (int) $p->equ_id_tra);
        }
    }
}
