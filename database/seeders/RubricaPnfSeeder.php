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
            // SEMESTRE 1: DOCENTE (60%) Y COMUNIDAD (40%)
            // ==========================================

            // 1. Rúbrica Tutor / Docente de Proyecto - Semestre 1 (60%)
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 1',
                'docente_proyecto',
                "Rúbrica del Docente de Proyecto - Semestre 1 ({$trayecto->tra_nombre})",
                60.00,
                [
                    ['nombre_criterio' => 'Asistencia, asesorías periódicas y cumplimiento del plan de trabajo', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 4.00, 'orden' => 1],
                    ['nombre_criterio' => 'Diagnóstico participativo, fundamentación teórica y contextualización comunitaria', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 6.00, 'orden' => 2],
                    ['nombre_criterio' => 'Diseño metodológico, definición de requerimientos y viabilidad de la propuesta', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 3],
                    ['nombre_criterio' => 'Estructura, redacción y coherencia técnica del informe de avance del semestre 1', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 4],
                    ['nombre_criterio' => 'Compromiso ético, iniciativa y trabajo en equipo del colectivo estudiantil', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 5],
                ]
            );

            // También mantener alias 'tutor' para Semestre 1
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 1',
                'tutor',
                "Rúbrica del Tutor Académico - Semestre 1 ({$trayecto->tra_nombre})",
                60.00,
                [
                    ['nombre_criterio' => 'Asistencia, asesorías periódicas y cumplimiento del plan de trabajo', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 4.00, 'orden' => 1],
                    ['nombre_criterio' => 'Diagnóstico participativo, fundamentación teórica y contextualización comunitaria', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 6.00, 'orden' => 2],
                    ['nombre_criterio' => 'Diseño metodológico, definición de requerimientos y viabilidad de la propuesta', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 3],
                    ['nombre_criterio' => 'Estructura, redacción y coherencia técnica del informe de avance del semestre 1', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 4],
                    ['nombre_criterio' => 'Compromiso ético, iniciativa y trabajo en equipo del colectivo estudiantil', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 5],
                ]
            );

            // 2. Rúbrica Comunidad - Semestre 1 (40%)
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 1',
                'comunidad',
                "Rúbrica de la Comunidad - Semestre 1 ({$trayecto->tra_nombre})",
                40.00,
                [
                    ['nombre_criterio' => 'Interacción, receptividad y respeto en las visitas comunitarias', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 7.00, 'orden' => 1],
                    ['nombre_criterio' => 'Identificación certera y priorización participativa de necesidades tecnológicas', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 7.00, 'orden' => 2],
                    ['nombre_criterio' => 'Socialización oportuna del diagnóstico y plan de acción con la comunidad', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 6.00, 'orden' => 3],
                    ['nombre_criterio' => 'Apreciación cualitativa de la comunidad sobre la empatía y compromiso del equipo', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 4],
                ]
            );

            // =========================================================================
            // SEMESTRE 2: MODELO OFICIAL UPTP (4 ACTORES: 40% + 30% + 20% + 10% = 100%)
            // =========================================================================

            // 3. Rúbrica Docente de Proyecto - Semestre 2 (40%)
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 2',
                'docente_proyecto',
                "Rúbrica Oficial del Docente de Proyecto - Semestre 2 ({$trayecto->tra_nombre})",
                40.00,
                [
                    ['nombre_criterio' => 'Asesorías semanales, seguimiento continuo y superación de observaciones', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 1],
                    ['nombre_criterio' => 'Arquitectura de software, buenas prácticas de desarrollo y calidad del código', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 2],
                    ['nombre_criterio' => 'Rigor metodológico, manuales técnicos y completitud del informe final', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 3],
                    ['nombre_criterio' => 'Innovación técnica, pertinencia social y articulación con los lineamientos del PNFI', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 4],
                    ['nombre_criterio' => 'Madurez investigativa y capacidad de resolución de retos tecnológicos', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 5],
                ]
            );

            // Alias 'tutor' en Semestre 2 con 40%
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 2',
                'tutor',
                "Rúbrica del Tutor / Docente - Semestre 2 ({$trayecto->tra_nombre})",
                40.00,
                [
                    ['nombre_criterio' => 'Asesorías semanales, seguimiento continuo y superación de observaciones', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 1],
                    ['nombre_criterio' => 'Arquitectura de software, buenas prácticas de desarrollo y calidad del código', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 2],
                    ['nombre_criterio' => 'Rigor metodológico, manuales técnicos y completitud del informe final', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 3],
                    ['nombre_criterio' => 'Innovación técnica, pertinencia social y articulación con los lineamientos del PNFI', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 4],
                    ['nombre_criterio' => 'Madurez investigativa y capacidad de resolución de retos tecnológicos', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 5],
                ]
            );

            // 4. Rúbrica Jurado Evaluador - Semestre 2 (30%)
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 2',
                'jurado',
                "Rúbrica Oficial de Jurados Evaluadores - Semestre 2 ({$trayecto->tra_nombre})",
                30.00,
                [
                    ['nombre_criterio' => 'Dominio conceptual, coherencia discursiva y argumentación técnica en la defensa', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 6.00, 'orden' => 1],
                    ['nombre_criterio' => 'Demostración en vivo del software (interfaz, funcionalidad, pruebas y seguridad)', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 8.00, 'orden' => 2],
                    ['nombre_criterio' => 'Calidad, sustento metodológico y aportes del informe técnico final', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 4.00, 'orden' => 3],
                    ['nombre_criterio' => 'Capacidad de respuesta y precisión ante las preguntas del comité evaluador', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 2.00, 'orden' => 4],
                    ['nombre_criterio' => 'Postura profesional, ética y solvencia investigativa del equipo', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 5],
                ]
            );

            // 5. Rúbrica Comunidad - Semestre 2 (20%)
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 2',
                'comunidad',
                "Rúbrica Oficial de la Comunidad - Semestre 2 ({$trayecto->tra_nombre})",
                20.00,
                [
                    ['nombre_criterio' => 'Funcionalidad y solución real de la necesidad comunitaria a través del software', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 7.00, 'orden' => 1],
                    ['nombre_criterio' => 'Capacitación a los usuarios, transferencia tecnológica y entrega de manuales', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 7.00, 'orden' => 2],
                    ['nombre_criterio' => 'Responsabilidad, cumplimiento y entrega formal del producto en el tiempo acordado', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 6.00, 'orden' => 3],
                    ['nombre_criterio' => 'Impacto social y grado de apropiación comunitaria de la tecnología desarrollada', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 4],
                ]
            );

            // 6. Rúbrica Estudiante (Autoevaluación / Coevaluación grupal) - Semestre 2 (10%)
            $this->crearOActualizarRubrica(
                $idTrayecto,
                'Semestre 2',
                'estudiante',
                "Rúbrica Oficial de Autoevaluación y Coevaluación Estudiantil - Semestre 2 ({$trayecto->tra_nombre})",
                10.00,
                [
                    ['nombre_criterio' => 'Autogestión, puntualidad y cumplimiento del cronograma interno del colectivo', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 1],
                    ['nombre_criterio' => 'Aporte técnico individual y colaboración equitativa en el desarrollo del software', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 2],
                    ['nombre_criterio' => 'Participación activa en actividades comunitarias y vinculación social', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 3],
                    ['nombre_criterio' => 'Responsabilidad ética, sentido de pertenencia y compromiso institucional PNFI', 'tipo_criterio' => 'cuantitativo', 'puntaje_maximo' => 5.00, 'orden' => 4],
                    ['nombre_criterio' => 'Reflexión cualitativa sobre el aprendizaje alcanzado y dificultades superadas', 'tipo_criterio' => 'cualitativo', 'puntaje_maximo' => 0.00, 'orden' => 5],
                ]
            );
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

    private function crearOActualizarRubrica(int $idTrayecto, string $semestre, string $rolEvaluador, string $titulo, float $pesoPorcentual, array $criterios): int
    {
        $rubrica = DB::table('rubricas')->where([
            'id_trayecto'   => $idTrayecto,
            'semestre'      => $semestre,
            'rol_evaluador' => $rolEvaluador,
        ])->first();

        if ($rubrica) {
            DB::table('rubricas')->where('id_rubrica', $rubrica->id_rubrica)->update([
                'titulo'          => $titulo,
                'peso_porcentual' => $pesoPorcentual,
                'activo'          => true,
                'updated_at'      => now(),
            ]);
            $idRubrica = $rubrica->id_rubrica;
            // Eliminar criterios anteriores para sincronizar exactamente
            DB::table('criterios_rubrica')->where('id_rubrica', $idRubrica)->delete();
        } else {
            $idRubrica = DB::table('rubricas')->insertGetId([
                'id_trayecto'     => $idTrayecto,
                'semestre'        => $semestre,
                'rol_evaluador'   => $rolEvaluador,
                'titulo'          => $titulo,
                'peso_porcentual' => $pesoPorcentual,
                'activo'          => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        foreach ($criterios as $c) {
            DB::table('criterios_rubrica')->insert([
                'id_rubrica'      => $idRubrica,
                'nombre_criterio' => $c['nombre_criterio'],
                'tipo_criterio'   => $c['tipo_criterio'],
                'puntaje_maximo'  => $c['puntaje_maximo'],
                'orden'           => $c['orden'],
            ]);
        }

        return $idRubrica;
    }
}
