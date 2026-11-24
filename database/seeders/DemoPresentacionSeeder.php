<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoPresentacionSeeder extends Seeder
{
    /**
     * Seeder Idempotente para Demostración Institucional en la UPTP "Juan de Jesús Montilla".
     *
     * 1. Normaliza los correos electrónicos en 'usuario' (minúsculas, sin acentos ni mayúsculas).
     * 2. Actualiza comunidades a entidades reales de Acarigua y Araure, Portuguesa.
     * 3. Garantiza período 2026-II activo en semana 12 y secciones sincronizadas.
     * 4. Reemplaza textos basura ('sdfsd', 'dsfd', 'dfd', 'dfs') y genera 3 equipos realistas
     *    en la sección del docente principal (Sección 331, Trayecto III, Semestre 6):
     *    - Equipo 1: En Ruta (Verde)
     *    - Equipo 2: Requiere Acelerar (Amarillo)
     *    - Equipo 3: Inviable / Riesgo Crítico (Rojo/Oscuro)
     */
    public function run(): void
    {
        DB::transaction(function () {
            // ── 1. NORMALIZACIÓN DE CORREOS EN USUARIO ──────────────────────
            $usuarios = DB::table('usuario')->get();
            $mapAcentos = [
                'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
                'ñ' => 'n', 'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            ];
            foreach ($usuarios as $u) {
                if (!empty($u->usu_email)) {
                    $emailLimpio = strtr(mb_strtolower(trim($u->usu_email), 'UTF-8'), $mapAcentos);
                    if ($emailLimpio !== $u->usu_email) {
                        DB::table('usuario')->where('usu_id', $u->usu_id)->update(['usu_email' => $emailLimpio]);
                    }
                }
            }

            // ── 2. COMUNIDADES REALES DE ACARIGUA Y ARAURE ─────────────────
            $this->call(ComunidadSeeder::class);

            // ── 3. PERÍODO ACADÉMICO ACTIVO 2026-II ────────────────────────
            $this->call(PeriodoAcademicoActivoSeeder::class);

            // ── 4. ASIGNACIÓN DEL ESQUEMA A LA SECCIÓN 331 ─────────────────
            if (Schema::hasTable('esquema_seccion') && Schema::hasTable('esquemas_evaluacion')) {
                $esqId = DB::table('esquemas_evaluacion')->where('esq_activo', 1)->value('esq_id') ?? 1;
                DB::table('esquema_seccion')->updateOrInsert(
                    [
                        'esq_sec_id_sec' => 1,
                        'esq_sec_id_esq' => $esqId,
                    ],
                    [
                        'esq_sec_lapso'  => '2026-II',
                        'esq_sec_activo' => 1,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                );
            }

            $idProfesor = 2; // Dexia Colmenarez (profesor@uptp.edu.ve)
            $idSec = 1;      // Sección 331 (Trayecto 3, Semestre 6)
            $idTra = 3;      // Trayecto 3

            // ── 5. EQUIPO 1: 🟢 EN RUTA (Excelente avance y cumplimiento) ──
            $idEq1 = 1;
            DB::table('equipo')->updateOrInsert(
                ['equ_id' => $idEq1],
                [
                    'equ_codigo'          => 'PNFI-T3-331-EQ01',
                    'equ_titulo'          => 'Sistema de Control de Inventario y Bienes Comunales (Batalla de Santa Inés)',
                    'equ_id_sec'          => $idSec,
                    'equ_id_tra'          => $idTra,
                    'equ_status'          => 1,
                    'equ_id_usu_created'  => $idProfesor,
                ]
            );

            // Integrantes Equipo 1 (Líder: Sergio Martens)
            DB::table('equipo_integrante')->where('ein_id_equ', $idEq1)->delete();
            $integrantesEq1 = [
                ['id_usu' => 3, 'es_lider' => true],   // Sergio Martens
                ['id_usu' => 20, 'es_lider' => false], // Marco Arpaia
                ['id_usu' => 21, 'es_lider' => false], // Juan Cordero
                ['id_usu' => 22, 'es_lider' => false], // Greinmer Valderrama
            ];
            foreach ($integrantesEq1 as $int) {
                DB::table('equipo_integrante')->insert([
                    'ein_id_equ'         => $idEq1,
                    'ein_id_usu'         => $int['id_usu'],
                    'ein_es_lider'       => $int['es_lider'],
                    'ein_fecha_ingreso'  => '2026-09-01',
                    'ein_status'         => 1,
                    'ein_id_usu_created' => $idProfesor,
                ]);
            }

            // Proyecto Comunidad Equipo 1
            DB::table('proyecto_comunidad')->updateOrInsert(
                ['pco_id_equ' => $idEq1],
                [
                    'pco_id_com'              => 10, // Comuna Batalla de Santa Inés
                    'pco_resumen'             => 'Control e inventario de suministros comunales en sector Durigua, Acarigua, Portuguesa.',
                    'pco_linea_investigacion' => 'Desarrollo de Software e Ingeniería Web Comunitaria',
                    'pco_id_tpr'              => 3, // Inventario
                    'pco_id_mpr'              => 1, // Nuevo
                    'pco_fecha_aprobacion'    => '2026-09-15',
                    'pco_fecha_limite'        => '2026-12-15',
                    'pco_status'              => 1,
                    'pco_id_usu_aprobado'     => $idProfesor,
                    'pco_id_usu_created'      => $idProfesor,
                ]
            );
            $pcoId1 = DB::table('proyecto_comunidad')->where('pco_id_equ', $idEq1)->value('pco_id');

            // Carta de Presentación Aprobada
            if (Schema::hasTable('carta_presentacion') && $pcoId1) {
                DB::table('carta_presentacion')->updateOrInsert(
                    ['cpr_id_pco' => $pcoId1],
                    [
                        'cpr_ruta_archivo'     => 'cartas/carta_aprobada_eq1.pdf',
                        'cpr_fecha_aprobacion' => '2026-09-18',
                        'cpr_fecha_generacion' => '2026-09-10',
                        'cpr_solicitada'       => 1,
                        'cpr_id_usu_created'   => 3,
                    ]
                );
            }

            // Tutor asignado
            if (Schema::hasTable('tutor')) {
                DB::table('tutor')->updateOrInsert(
                    ['tut_id_equ' => $idEq1],
                    [
                        'tut_id_usu'         => $idProfesor,
                        'tut_status'         => 1,
                        'tut_id_usu_created' => $idProfesor,
                    ]
                );
            }

            // Puntos de Control y Seguimientos Equipo 1
            $this->limpiarSeguimientosEquipo($idEq1);
            $puc1_1 = $this->crearPuntoControl($idEq1, $idTra, 'Punto 1 - Diagnóstico y Requerimientos', 3, '2026-09-22', $idProfesor);
            $puc1_2 = $this->crearPuntoControl($idEq1, $idTra, 'Punto 2 - Arquitectura y Base de Datos', 6, '2026-10-14', $idProfesor);
            $puc1_3 = $this->crearPuntoControl($idEq1, $idTra, 'Punto 3 - Prototipo Funcional de Inventario', 10, '2026-11-10', $idProfesor);
            $this->crearPuntoControl($idEq1, $idTra, 'Punto 4 - Pruebas Integrales y Transferencia', 14, '2026-12-08', $idProfesor);

            $seq1_1 = $this->crearSeguimiento($idEq1, $puc1_1, '2026-09-22', 'positivo', 100, 1, 'Encuentro altamente satisfactorio. Instrumentos aplicados correctamente.');
            $seq1_2 = $this->crearSeguimiento($idEq1, $puc1_2, '2026-10-14', 'positivo', 95, 2, 'Modelo de datos en tercera forma normal aprobado sin objeciones.');
            $seq1_3 = $this->crearSeguimiento($idEq1, $puc1_3, '2026-11-10', 'positivo', 92, 3, 'Prototipo probado con voceros de la comuna con resultados positivos.');

            // Asistencias Equipo 1 (>90%, con traza de edición justificada)
            $this->registrarAsistencias($seq1_1, $idProfesor, [
                ['id_usu' => 3, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 20, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 21, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 22, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
            ]);
            $this->registrarAsistencias($seq1_2, $idProfesor, [
                ['id_usu' => 3, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 20, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 21, 'asistio' => 0, 'justificado' => 1, 'participo' => 0, 'motivo' => 'Presentó constancia médica validada por departamento de bienestar estudiantil.'],
                ['id_usu' => 22, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
            ]);
            $this->registrarAsistencias($seq1_3, $idProfesor, [
                ['id_usu' => 3, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 20, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 21, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 22, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
            ]);

            // Acuerdos Equipo 1 (Ambos rectificados)
            $this->crearAcuerdo($seq1_1, 'Ajustar la cardinalidad de la tabla de kardex comunitario.', 'rectificado', '2026-10-01');
            $this->crearAcuerdo($seq1_2, 'Incorporar reporte en PDF con firma del responsable de bodega.', 'rectificado', '2026-10-25');

            // Entregables PNFI Equipo 1 (5 de 6 aprobados = 83.3% > 70%)
            DB::table('entregable')->where('entr_id_equ', $idEq1)->delete();
            $this->crearEntregable($idEq1, 32, 'Solucion_Tecnica_Inventario_vFinal.zip', 'entregables/eq1/solucion.zip', true, $idProfesor);
            $this->crearEntregable($idEq1, 33, 'Informe_Tecnico_Implementacion_Santa_Ines.pdf', 'entregables/eq1/informe.pdf', true, $idProfesor);
            $this->crearEntregable($idEq1, 34, 'Actas_Capacitacion_Comunitaria_Firmadas.pdf', 'entregables/eq1/actas.pdf', true, $idProfesor);
            $this->crearEntregable($idEq1, 35, 'Presentacion_Socializacion_Santa_Ines.pptx', 'entregables/eq1/presentacion.pptx', true, $idProfesor);
            $this->crearEntregable($idEq1, 36, 'Acta_Socializacion_Comunidad_Jurado.pdf', 'entregables/eq1/acta_soc.pdf', true, $idProfesor);


            // ── 6. EQUIPO 2: 🟡 ACELERAR (Avance intermedio, 2 acuerdos pendientes) ──
            $idEq2 = 2;
            DB::table('equipo')->updateOrInsert(
                ['equ_id' => $idEq2],
                [
                    'equ_codigo'          => 'PNFI-T3-331-EQ02',
                    'equ_titulo'          => 'Gestión Web de Citas y Triaje Médico (CDI Campo Lindo)',
                    'equ_id_sec'          => $idSec,
                    'equ_id_tra'          => $idTra,
                    'equ_status'          => 1,
                    'equ_id_usu_created'  => $idProfesor,
                ]
            );

            // Integrantes Equipo 2 (Líder estudiantil: Valentina García)
            DB::table('equipo_integrante')->where('ein_id_equ', $idEq2)->delete();
            $integrantesEq2 = [
                ['id_usu' => 13, 'es_lider' => true],  // Valentina García
                ['id_usu' => 14, 'es_lider' => false], // Diego Sánchez
                ['id_usu' => 15, 'es_lider' => false], // Andrea Fernández
                ['id_usu' => 16, 'es_lider' => false], // Miguel Orozco
            ];
            foreach ($integrantesEq2 as $int) {
                DB::table('equipo_integrante')->insert([
                    'ein_id_equ'         => $idEq2,
                    'ein_id_usu'         => $int['id_usu'],
                    'ein_es_lider'       => $int['es_lider'],
                    'ein_fecha_ingreso'  => '2026-09-01',
                    'ein_status'         => 1,
                    'ein_id_usu_created' => $idProfesor,
                ]);
            }

            // Proyecto Comunidad Equipo 2
            DB::table('proyecto_comunidad')->updateOrInsert(
                ['pco_id_equ' => $idEq2],
                [
                    'pco_id_com'              => 5, // CDI Campo Lindo
                    'pco_resumen'             => 'Gestión de citas y triaje médico para el CDI y Ambulatorio Campo Lindo de Acarigua.',
                    'pco_linea_investigacion' => 'Sistemas de Información en Salud Pública',
                    'pco_id_tpr'              => 4, // Sistema Administrativo
                    'pco_id_mpr'              => 1, // Nuevo
                    'pco_fecha_aprobacion'    => '2026-09-20',
                    'pco_fecha_limite'        => '2026-12-20',
                    'pco_status'              => 1,
                    'pco_id_usu_aprobado'     => $idProfesor,
                    'pco_id_usu_created'      => $idProfesor,
                ]
            );

            // Puntos de Control y Seguimientos Equipo 2
            $this->limpiarSeguimientosEquipo($idEq2);
            $puc2_1 = $this->crearPuntoControl($idEq2, $idTra, 'Punto 1 - Requerimientos Médicos y Flujos de Triaje', 4, '2026-09-29', $idProfesor);
            $puc2_2 = $this->crearPuntoControl($idEq2, $idTra, 'Punto 2 - Módulo de Registro y Validación de Historias', 8, '2026-10-27', $idProfesor);
            $puc2_3 = $this->crearPuntoControl($idEq2, $idTra, 'Punto 3 - Interfaz de Citas y Reportes Estadísticos', 11, '2026-11-17', $idProfesor);

            $seq2_1 = $this->crearSeguimiento($idEq2, $puc2_1, '2026-09-29', 'positivo', 90, 1, 'Requerimientos levantados con el equipo médico.');
            $seq2_2 = $this->crearSeguimiento($idEq2, $puc2_2, '2026-10-27', 'neutral', 70, 2, 'Observaciones en validación de historias duplicadas.');
            $seq2_3 = $this->crearSeguimiento($idEq2, $puc2_3, '2026-11-17', 'neutral', 75, 4, 'Módulo de citas operativo pero con retraso en el cierre de pruebas.');

            // Asistencias Equipo 2 (~83%)
            $this->registrarAsistencias($seq2_1, $idProfesor, [
                ['id_usu' => 13, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 14, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 15, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 16, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
            ]);
            $this->registrarAsistencias($seq2_2, $idProfesor, [
                ['id_usu' => 13, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 14, 'asistio' => 0, 'justificado' => 1, 'participo' => 0, 'motivo' => 'Laboral justificado.'],
                ['id_usu' => 15, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 16, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
            ]);
            $this->registrarAsistencias($seq2_3, $idProfesor, [
                ['id_usu' => 13, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 14, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
                ['id_usu' => 15, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 16, 'asistio' => 1, 'justificado' => 0, 'participo' => 1],
            ]);

            // Acuerdos Equipo 2 (2 Acuerdos pendientes -> dispara 'acelerar')
            $this->crearAcuerdo($seq2_2, 'Subsanar la validación de duplicidad de historias clínicas en el formulario de triaje.', 'en_proceso', '2026-11-26');
            $this->crearAcuerdo($seq2_3, 'Presentar matriz de pruebas con 100 historias médicas validadas por el personal de enfermería.', 'pendiente', '2026-11-30');

            // Entregables PNFI Equipo 2 (3 de 6 aprobados = 50.0% < 70% en semana 12)
            DB::table('entregable')->where('entr_id_equ', $idEq2)->delete();
            $this->crearEntregable($idEq2, 32, 'Prototipo_Gestion_Citas_v1.zip', 'entregables/eq2/prototipo.zip', true, $idProfesor);
            $this->crearEntregable($idEq2, 33, 'Informe_Tecnico_Parcial_Salud.pdf', 'entregables/eq2/informe.pdf', true, $idProfesor);
            $this->crearEntregable($idEq2, 34, 'Borrador_Acta_Capacitacion_Salud.pdf', 'entregables/eq2/borrador.pdf', true, $idProfesor);


            // ── 7. EQUIPO 3: 🔴 / ⛔ INVIABLE (4 encuentros negativos seguidos, asist <65%) ──
            $idEq3 = 3;
            DB::table('equipo')->updateOrInsert(
                ['equ_id' => $idEq3],
                [
                    'equ_codigo'          => 'PNFI-T3-331-EQ03',
                    'equ_titulo'          => 'Sistema Automatizado de Matrícula (E.T.A. Payara)',
                    'equ_id_sec'          => $idSec,
                    'equ_id_tra'          => $idTra,
                    'equ_status'          => 1,
                    'equ_id_usu_created'  => $idProfesor,
                ]
            );

            // Integrantes Equipo 3
            DB::table('equipo_integrante')->where('ein_id_equ', $idEq3)->delete();
            $integrantesEq3 = [
                ['id_usu' => 17, 'es_lider' => true],  // Gabriela Mendoza
                ['id_usu' => 18, 'es_lider' => false], // Fernando Castro
                ['id_usu' => 19, 'es_lider' => false], // Stephanie Navarro
                ['id_usu' => 23, 'es_lider' => false], // Ángel Aguilar
            ];
            foreach ($integrantesEq3 as $int) {
                DB::table('equipo_integrante')->insert([
                    'ein_id_equ'         => $idEq3,
                    'ein_id_usu'         => $int['id_usu'],
                    'ein_es_lider'       => $int['es_lider'],
                    'ein_fecha_ingreso'  => '2026-09-01',
                    'ein_status'         => 1,
                    'ein_id_usu_created' => $idProfesor,
                ]);
            }

            // Proyecto Comunidad Equipo 3
            DB::table('proyecto_comunidad')->updateOrInsert(
                ['pco_id_equ' => $idEq3],
                [
                    'pco_id_com'              => 9, // E.T.A. Payara
                    'pco_resumen'             => 'Control automatizado de matrícula y expedientes para la E.T.A. Payara, Municipio Páez.',
                    'pco_linea_investigacion' => 'Gestión Académica y Control de Estudios',
                    'pco_id_tpr'              => 1, // Control de Estudios
                    'pco_id_mpr'              => 1, // Nuevo
                    'pco_fecha_aprobacion'    => '2026-09-22',
                    'pco_fecha_limite'        => '2026-12-10',
                    'pco_status'              => 1,
                    'pco_id_usu_aprobado'     => $idProfesor,
                    'pco_id_usu_created'      => $idProfesor,
                ]
            );

            // Puntos de Control y Seguimientos Equipo 3 (4 encuentros negativos)
            $this->limpiarSeguimientosEquipo($idEq3);
            $puc3_1 = $this->crearPuntoControl($idEq3, $idTra, 'Punto 1 - Levantamiento y Alcance Payara', 3, '2026-09-23', $idProfesor);
            $puc3_2 = $this->crearPuntoControl($idEq3, $idTra, 'Punto 2 - Arquitectura de Datos de Matrícula', 6, '2026-10-15', $idProfesor);
            $puc3_3 = $this->crearPuntoControl($idEq3, $idTra, 'Punto 3 - Módulos de Inscripción y Validación', 9, '2026-11-04', $idProfesor);
            $puc3_4 = $this->crearPuntoControl($idEq3, $idTra, 'Punto 4 - Evaluación de Cierre de Fases', 12, '2026-11-19', $idProfesor);

            $seq3_1 = $this->crearSeguimiento($idEq3, $puc3_1, '2026-09-23', 'negativo', 25, 1, 'El equipo no presentó instrumentos de recolección validados.');
            $seq3_2 = $this->crearSeguimiento($idEq3, $puc3_2, '2026-10-15', 'negativo', 20, 2, 'Modelo de datos incompleto y sin normalizar. Ausencia injustificada.');
            $seq3_3 = $this->crearSeguimiento($idEq3, $puc3_3, '2026-11-04', 'negativo', 15, 4, 'Sin avances en código ni informe. Compromisos previos incumplidos.');
            $seq3_4 = $this->crearSeguimiento($idEq3, $puc3_4, '2026-11-19', 'negativo', 10, 2, 'Incumplimiento crítico de ruta. Se recomienda evaluación formal de repetición o retiro.');

            // Asistencias Equipo 3 (<65%, asistencia crítica)
            $this->registrarAsistencias($seq3_1, $idProfesor, [
                ['id_usu' => 17, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 18, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 19, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 23, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
            ]);
            $this->registrarAsistencias($seq3_2, $idProfesor, [
                ['id_usu' => 17, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 18, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 19, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 23, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
            ]);
            $this->registrarAsistencias($seq3_3, $idProfesor, [
                ['id_usu' => 17, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 18, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 19, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 23, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
            ]);
            $this->registrarAsistencias($seq3_4, $idProfesor, [
                ['id_usu' => 17, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 18, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 19, 'asistio' => 0, 'justificado' => 0, 'participo' => 0],
                ['id_usu' => 23, 'asistio' => 1, 'justificado' => 0, 'participo' => 0],
            ]);

            // Acuerdos Equipo 3 (3 acuerdos vencidos sin rectificar)
            $this->crearAcuerdo($seq3_1, 'Entregar diagrama entidad-relación normalizado hasta 3FN.', 'pendiente', '2026-10-10');
            $this->crearAcuerdo($seq3_2, 'Presentar bitácora de visitas a la E.T.A. Payara con sello y firma de dirección.', 'pendiente', '2026-10-28');
            $this->crearAcuerdo($seq3_3, 'Subir repositorio de control de versiones con la estructura base del sistema.', 'pendiente', '2026-11-12');

            // Entregables PNFI Equipo 3 (0 aprobados)
            DB::table('entregable')->where('entr_id_equ', $idEq3)->delete();
            $this->crearEntregable($idEq3, 32, 'Borrador_Incompleto_Payara.pdf', 'entregables/eq3/borrador.pdf', false, $idProfesor, 'Documento incompleto sin respaldo metodológico ni validación comunitaria.');
        });
    }

    private function crearPuntoControl(int $idEquipo, int $idTrayecto, string $nombre, int $semana, string $fechaLimite, int $idProf): int
    {
        $id = DB::table('punto_control')->insertGetId([
            'puc_id_equ'         => $idEquipo,
            'puc_id_tra'         => $idTrayecto,
            'puc_nombre'         => $nombre,
            'puc_descripcion'    => "Seguimiento académico formal correspondiente a la semana {$semana}.",
            'puc_semana'         => $semana,
            'puc_fecha_limite'   => $fechaLimite,
            'puc_dias_aviso'     => 3,
            'puc_orden'          => $semana,
            'puc_status'         => 1,
            'puc_id_usu_created' => $idProf,
            'puc_created_at'     => now(),
            'puc_updated_at'     => now(),
        ]);
        return $id;
    }

    private function crearSeguimiento(int $idEquipo, int $idPuc, string $fecha, string $estado, int $avance, int $tipoEncuentro, string $obs): int
    {
        $id = DB::table('seguimiento_equipo')->insertGetId([
            'seq_id_equ'             => $idEquipo,
            'seq_id_puc'             => $idPuc,
            'seq_fecha_registro'     => $fecha,
            'seq_estado_semaforo'    => $estado,
            'seq_valoracion_avance'  => $avance,
            'seq_id_tipo_encuentro'  => $tipoEncuentro,
            'seq_cumplido'           => 1,
            'seq_cumplido_a_tiempo'  => 1,
            'seq_observaciones'      => $obs,
            'seq_status'             => 1,
            'seq_created_at'         => now(),
            'seq_updated_at'         => now(),
        ]);
        return $id;
    }

    private function registrarAsistencias(int $idSeq, int $idProf, array $asistencias): void
    {
        foreach ($asistencias as $a) {
            DB::table('asistencia_punto_control')->insert([
                'apc_id_seq'          => $idSeq,
                'apc_id_usu'          => $a['id_usu'],
                'apc_asistio'         => $a['asistio'],
                'apc_justificado'     => $a['justificado'] ?? 0,
                'apc_participo'       => $a['participo'] ?? 0,
                'apc_motivo_edicion'  => $a['motivo'] ?? null,
                'apc_updated_at'      => !empty($a['motivo']) ? now() : null,
                'apc_id_usu_updated'  => !empty($a['motivo']) ? $idProf : null,
                'apc_id_usu_created'  => $idProf,
                'apc_created_at'      => now(),
            ]);
        }
    }

    private function crearAcuerdo(int $idSeq, string $descripcion, string $estado, string $fechaLimite): void
    {
        if (Schema::hasTable('acuerdos_encuentro')) {
            DB::table('acuerdos_encuentro')->insert([
                'acue_id_seq'       => $idSeq,
                'acue_descripcion'  => $descripcion,
                'acue_estado'       => $estado,
                'acue_fecha_limite' => $fechaLimite,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    private function crearEntregable(int $idEquipo, int $idCep, string $nombre, string $ruta, ?bool $aprobado, int $idProf, ?string $obsRechazo = null): void
    {
        DB::table('entregable')->insert([
            'entr_id_equ'              => $idEquipo,
            'entr_id_cep'              => $idCep,
            'entr_nombre_archivo'      => $nombre,
            'entr_ruta'                => $ruta,
            'entr_version'             => '1.0',
            'entr_es_entregable_final' => false,
            'entr_a_tiempo'            => true,
            'entr_aprobado'            => $aprobado,
            'entr_fecha_aprobacion'    => $aprobado === true ? now()->toDateString() : null,
            'entr_observacion_rechazo' => $obsRechazo,
            'entr_fecha_subida'        => now()->toDateString(),
            'entr_status'              => 1,
            'entr_id_usu_created'      => 3,
            'entr_id_usu_aprobado'     => $aprobado === true ? $idProf : null,
        ]);
    }

    private function limpiarSeguimientosEquipo(int $idEquipo): void
    {
        $pucs = DB::table('punto_control')->where('puc_id_equ', $idEquipo)->pluck('puc_id');
        $seqs = DB::table('seguimiento_equipo')->whereIn('seq_id_puc', $pucs)->pluck('seq_id');

        if (Schema::hasTable('notificacion')) {
            DB::table('notificacion')->whereIn('not_id_puc', $pucs)->delete();
        }
        if (Schema::hasTable('entregable')) {
            DB::table('entregable')->whereIn('entr_id_puc', $pucs)->update(['entr_id_puc' => null]);
        }
        if (Schema::hasTable('evaluacion_individual') && Schema::hasColumn('evaluacion_individual', 'evi_id_puc')) {
            DB::table('evaluacion_individual')->whereIn('evi_id_puc', $pucs)->delete();
        }
        if (Schema::hasTable('asistencia_punto_control')) {
            DB::table('asistencia_punto_control')->whereIn('apc_id_seq', $seqs)->delete();
        }
        if (Schema::hasTable('acuerdos_encuentro')) {
            DB::table('acuerdos_encuentro')->whereIn('acue_id_seq', $seqs)->delete();
        }
        DB::table('seguimiento_equipo')->whereIn('seq_id', $seqs)->delete();
        DB::table('punto_control')->whereIn('puc_id', $pucs)->delete();
    }
}
