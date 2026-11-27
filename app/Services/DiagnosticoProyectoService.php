<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnosticoProyectoService
{
    /**
     * Diagnóstico integral de un equipo para una semana dada.
     * Retorna semáforo, porcentajes, alertas de ruta crítica y viabilidad.
     */
    public function diagnosticarEquipo(int $idEquipo, ?int $semanaActual = null): array
    {
        if ($semanaActual === null || $semanaActual <= 0) {
            $semanaActual = $this->calcularSemanaActualPeriodo();
        }

        $equipo = DB::table('equipo')->where('equ_id', $idEquipo)->first();

        $codigo       = $equipo->equ_codigo ?? ('EQ-' . $idEquipo);
        $titulo       = $equipo->equ_titulo ?? '';
        $nombreEquipo = !empty($titulo) ? "{$codigo} - {$titulo}" : $codigo;
        $idTra        = $equipo->equ_id_tra ?? null;
        $idSec        = $equipo->equ_id_sec ?? null;
        $semestre     = $idSec ? $this->obtenerSemestreSeccion((int) $idSec) : 'Semestre 1';

        $statsEntregables  = $this->calcularAvanceEntregables($idEquipo, $idTra, $semestre);
        $avanceEntregables = $statsEntregables['porcentaje'];
        $asistencia        = $this->calcularAsistencia($idEquipo);
        $correccionesPend  = $this->contarCorreccionesPendientes($idEquipo);
        $estadoEncuentros  = $this->analizarEncuentros($idEquipo);
        $avanceSeg         = $this->calcularAvanceSeguimientos($idEquipo);

        // Situación del Proyecto medida por porcentaje ponderado (0% - 100%)
        $totalEsperados = $statsEntregables['total_esperados'] ?? 0;
        if ($avanceSeg['total_seguimientos'] > 0) {
            $componenteTecnico = $totalEsperados > 0
                ? round(($avanceEntregables * 0.50) + ($avanceSeg['promedio_avance'] * 0.50), 1)
                : $avanceSeg['promedio_avance'];
        } else {
            $componenteTecnico = $totalEsperados > 0 ? $avanceEntregables : 100.0;
        }
        $componenteCumplimiento = max(0.0, round($avanceSeg['tasa_cumplimiento'] - ($correccionesPend * 5), 1));
        $porcentajeSituacion = round(
            min(100.0, max(0.0, ($componenteTecnico * 0.50) + ($asistencia * 0.30) + ($componenteCumplimiento * 0.20))),
            1
        );

        $semaforo          = $this->determinarSemaforo($porcentajeSituacion, $avanceEntregables, $asistencia, $correccionesPend, $estadoEncuentros, $semanaActual);
        $mensaje           = $this->generarMensaje($semaforo, $porcentajeSituacion, $avanceEntregables, $asistencia, $correccionesPend, $semanaActual);
        $viable            = $this->evaluarViabilidad($semaforo, $porcentajeSituacion, $avanceEntregables, $asistencia, $semanaActual);

        return [
            'id_equipo'                     => $idEquipo,
            'codigo_equipo'                 => $codigo,
            'titulo_equipo'                 => $titulo,
            'nombre_equipo'                 => $nombreEquipo,
            'semestre'                      => $semestre,
            'semaforo'                      => $semaforo,              // 'en_ruta' | 'acelerar' | 'riesgo_alto' | 'inviable'
            'porcentaje_situacion'          => $porcentajeSituacion,
            'avance_seguimientos'           => $avanceSeg['promedio_avance'],
            'color'                         => $this->colorSemaforo($semaforo),
            'avance_entregables'            => $avanceEntregables,     // float 0-100 (aprobados)
            'avance_subidos'                => $statsEntregables['porcentaje_subidos'] ?? 0.0,
            'entregables_stats'             => $statsEntregables,
            'asistencia_promedio'           => $asistencia,            // float 0-100
            'correcciones_pendientes'       => $correccionesPend,      // int
            'encuentros_negativos_seguidos' => $estadoEncuentros['negativos_seguidos'],
            'ultimos_encuentros'            => $estadoEncuentros['ultimos'],
            'mensaje'                       => $mensaje,
            'es_viable'                     => $viable,
            'semana_actual'                 => $semanaActual,
        ];
    }

    /**
     * Calcula la semana académica actual basada en el período activo en periodos_academicos.
     * Retorna un entero entre 1 y 18.
     */
    public function calcularSemanaActualPeriodo(): int
    {
        if (Schema::hasTable('periodos_academicos')) {
            $periodo = DB::table('periodos_academicos')
                ->where('activo', 1)
                ->first();

            if ($periodo && !empty($periodo->fecha_inicio)) {
                $inicio = \Carbon\Carbon::parse($periodo->fecha_inicio)->startOfDay();
                $hoy = now()->startOfDay();
                if ($hoy->greaterThanOrEqualTo($inicio)) {
                    $dias = $inicio->diffInDays($hoy);
                    $semana = (int) floor($dias / 7) + 1;
                    return min(18, max(1, $semana));
                }
            }
        }

        return 1;
    }

    /**
     * Determina el semestre correspondiente a una sección.
     * Mapea valores numéricos de trayecto/carrera (ej. 2, 4, 6, 8 => 'Semestre 2', 1, 3, 5, 7 => 'Semestre 1').
     * Si sec_semestre es NULL, consulta el período académico activo en periodos_academicos
     * mapeando 'Impar' => 'Semestre 1' y 'Par' => 'Semestre 2'.
     */
    public function obtenerSemestreSeccion(int $idSeccion): string
    {
        if (Schema::hasTable('seccion')) {
            $seccion = DB::table('seccion')
                ->where('sec_id', $idSeccion)
                ->select('sec_semestre')
                ->first();

            if (!empty($seccion?->sec_semestre)) {
                $val = $seccion->sec_semestre;
                if (is_numeric($val)) {
                    return ((int) $val % 2 === 0) ? 'Semestre 2' : 'Semestre 1';
                }
                if (str_contains(strtolower((string) $val), '2')) {
                    return 'Semestre 2';
                }
                return 'Semestre 1';
            }
        }

        // Fallback a período académico activo
        if (Schema::hasTable('periodos_academicos')) {
            $periodoActivo = DB::table('periodos_academicos')
                ->where('activo', 1)
                ->first();

            if ($periodoActivo && !empty($periodoActivo->tipo_semestre)) {
                return match (trim(strtolower($periodoActivo->tipo_semestre))) {
                    'par'   => 'Semestre 2',
                    default => 'Semestre 1',
                };
            }
        }

        return 'Semestre 1';
    }

    /**
     * Calcula el porcentaje de avance en entregables aprobados y subidos
     * contrastando contra el catálogo PNFI si está disponible.
     */
    private function calcularAvanceEntregables(int $idEquipo, ?int $idTrayecto, string $semestre): array
    {
        if (!Schema::hasTable('entregable')) {
            return [
                'porcentaje'          => 0.0,
                'porcentaje_subidos'  => 0.0,
                'total_esperados'     => 0,
                'aprobados'           => 0,
                'subidos'             => 0,
                'pendientes_revision' => 0,
                'rechazados'          => 0,
            ];
        }

        // Consultar catálogo oficial PNFI según trayecto y semestre
        $totalEsperados = 0;
        if ($idTrayecto && Schema::hasTable('catalogo_entregables_pnfi')) {
            $totalEsperados = DB::table('catalogo_entregables_pnfi')
                ->where('cep_id_trayecto', $idTrayecto)
                ->where('cep_semestre', $semestre)
                ->where('cep_activo', 1)
                ->count();
        }

        // Registros de entregables reales de este equipo
        $entregables = DB::table('entregable')
            ->where('entr_id_equ', $idEquipo)
            ->where('entr_status', 1)
            ->get();

        $aprobados          = $entregables->where('entr_aprobado', 1)->count();
        $subidos            = $entregables->filter(fn($e) => !empty($e->entr_ruta))->count();
        $pendientesRevision = $entregables->filter(fn($e) => is_null($e->entr_aprobado) && !empty($e->entr_ruta))->count();
        $rechazados         = $entregables->where('entr_aprobado', 0)->count();

        $divisor = $totalEsperados > 0 ? $totalEsperados : max(1, $entregables->count());

        $porcentajeAprobados = min(100.0, round(($aprobados / $divisor) * 100, 1));
        $porcentajeSubidos   = min(100.0, round(($subidos / $divisor) * 100, 1));

        return [
            'porcentaje'          => $porcentajeAprobados,
            'porcentaje_subidos'  => $porcentajeSubidos,
            'total_esperados'     => $totalEsperados > 0 ? $totalEsperados : $entregables->count(),
            'aprobados'           => $aprobados,
            'subidos'             => $subidos,
            'pendientes_revision' => $pendientesRevision,
            'rechazados'          => $rechazados,
        ];
    }

    /**
     * Calcula el porcentaje de asistencia general del equipo en los puntos de control.
     */
    private function calcularAsistencia(int $idEquipo): float
    {
        if (!Schema::hasTable('asistencia_punto_control') || !Schema::hasTable('seguimiento_equipo')) {
            return 100.0;
        }

        $registros = DB::table('asistencia_punto_control as apc')
            ->join('seguimiento_equipo as seq', 'apc.apc_id_seq', '=', 'seq.seq_id')
            ->where('seq.seq_id_equ', $idEquipo)
            ->count();

        if ($registros === 0) {
            return 100.0; // Sin faltas registradas aún
        }

        $asistieron = DB::table('asistencia_punto_control as apc')
            ->join('seguimiento_equipo as seq', 'apc.apc_id_seq', '=', 'seq.seq_id')
            ->where('seq.seq_id_equ', $idEquipo)
            ->where(function ($q) {
                $q->where('apc.apc_asistio', 1)
                  ->orWhere('apc.apc_justificado', 1);
            })
            ->count();

        return round(($asistieron / $registros) * 100, 1);
    }

    /**
     * Cuenta correcciones y acuerdos de encuentro aún pendientes o en proceso.
     * Utiliza estrictamente el ENUM de la base de datos: 'pendiente', 'en_proceso', 'rectificado'.
     */
    private function contarCorreccionesPendientes(int $idEquipo): int
    {
        if (!Schema::hasTable('acuerdos_encuentro') || !Schema::hasTable('seguimiento_equipo')) {
            return 0;
        }

        try {
            return DB::table('acuerdos_encuentro as ae')
                ->join('seguimiento_equipo as seq', 'ae.acue_id_seq', '=', 'seq.seq_id')
                ->where('seq.seq_id_equ', $idEquipo)
                ->whereIn('ae.acue_estado', ['pendiente', 'en_proceso'])
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Traduce el desempeño cualitativo del baremo literal al Semáforo de Ruta Crítica:
     * ('en_ruta' | 'acelerar' | 'riesgo_alto' | 'inviable').
     */
    public function traducirDesempenoCualitativoASemaforo(?string $literal, ?bool $aprobado, ?string $nivelLogro = null): string
    {
        $lit = strtoupper(trim((string) $literal));
        $nivel = mb_strtolower(trim((string) $nivelLogro), 'UTF-8');

        if ($lit === 'D' || str_contains($nivel, 'deficiente') || str_contains($nivel, 'no logrado')) {
            return 'riesgo_alto';
        }
        if ($lit === 'C' || $aprobado === false || str_contains($nivel, 'proceso') || str_contains($nivel, 'nivelaci')) {
            return 'acelerar';
        }
        if (in_array($lit, ['A', 'B'], true) || $aprobado === true || str_contains($nivel, 'excelente') || str_contains($nivel, 'bueno') || str_contains($nivel, 'aprobado')) {
            return 'en_ruta';
        }

        return 'en_ruta';
    }

    /**
     * Analiza el historial reciente de valoraciones de encuentros (incluyendo baremo cualitativo).
     */
    private function analizarEncuentros(int $idEquipo): array
    {
        if (!Schema::hasTable('seguimiento_equipo') || !Schema::hasColumn('seguimiento_equipo', 'seq_estado_semaforo')) {
            return ['negativos_seguidos' => 0, 'reprobados_cualitativos' => 0, 'ultimos' => []];
        }

        try {
            $tieneLiteral = Schema::hasColumn('seguimiento_equipo', 'seq_valoracion_literal');
            $cols = ['seq_estado_semaforo'];
            if ($tieneLiteral) {
                $cols[] = 'seq_valoracion_literal';
                $cols[] = 'seq_aprobado_cualitativo';
                $cols[] = 'seq_nivel_logro';
            }

            $registros = DB::table('seguimiento_equipo')
                ->where('seq_id_equ', $idEquipo)
                ->orderBy('seq_id', 'desc')
                ->take(5)
                ->get($cols);

            $ultimos = [];
            $negativos = 0;
            $reprobadosCualitativos = 0;

            foreach ($registros as $reg) {
                $estado = $reg->seq_estado_semaforo;
                if ($tieneLiteral && (!empty($reg->seq_valoracion_literal) || $reg->seq_aprobado_cualitativo !== null)) {
                    $estado = $this->traducirDesempenoCualitativoASemaforo(
                        $reg->seq_valoracion_literal ?? null,
                        $reg->seq_aprobado_cualitativo !== null ? (bool) $reg->seq_aprobado_cualitativo : null,
                        $reg->seq_nivel_logro ?? null
                    );
                    if ($reg->seq_aprobado_cualitativo === 0 || $reg->seq_aprobado_cualitativo === false) {
                        $reprobadosCualitativos++;
                    }
                }
                if ($estado) {
                    $ultimos[] = $estado;
                }
            }

            foreach ($ultimos as $estado) {
                if (in_array($estado, ['negativo', 'no_conforme', 'no_viable', 'inviable', 'riesgo_alto'])) {
                    $negativos++;
                } else {
                    break;
                }
            }

            return [
                'negativos_seguidos'      => $negativos,
                'reprobados_cualitativos' => $reprobadosCualitativos,
                'ultimos'                 => $ultimos,
            ];
        } catch (\Exception $e) {
            return ['negativos_seguidos' => 0, 'reprobados_cualitativos' => 0, 'ultimos' => []];
        }
    }

    /**
     * Calcula el promedio de avance (%) y la tasa de cumplimiento desde seguimiento_equipo.
     */
    private function calcularAvanceSeguimientos(int $idEquipo): array
    {
        if (!Schema::hasTable('seguimiento_equipo')) {
            return ['total_seguimientos' => 0, 'promedio_avance' => 100.0, 'tasa_cumplimiento' => 100.0];
        }

        $tieneLiteral = Schema::hasColumn('seguimiento_equipo', 'seq_valoracion_literal');
        $tieneAvance  = Schema::hasColumn('seguimiento_equipo', 'seq_valoracion_avance');
        $cols = ['seq_cumplido'];
        if ($tieneLiteral) {
            $cols[] = 'seq_valoracion_literal';
        }
        if ($tieneAvance) {
            $cols[] = 'seq_valoracion_avance';
        }

        $registros = DB::table('seguimiento_equipo')
            ->where('seq_id_equ', $idEquipo)
            ->get($cols);

        if ($registros->isEmpty()) {
            return ['total_seguimientos' => 0, 'promedio_avance' => 100.0, 'tasa_cumplimiento' => 100.0];
        }

        $mapaLiteral = ['A' => 95.0, 'B' => 80.0, 'C' => 60.0, 'D' => 35.0];
        $sumaAvance = 0.0;
        $cumplidos = 0;

        foreach ($registros as $reg) {
            if ($reg->seq_cumplido) {
                $cumplidos++;
            }

            if ($tieneLiteral && !empty($reg->seq_valoracion_literal)) {
                $lit = strtoupper(trim((string) $reg->seq_valoracion_literal));
                $sumaAvance += $mapaLiteral[$lit] ?? ($reg->seq_cumplido ? 75.0 : 30.0);
            } elseif ($tieneAvance && $reg->seq_valoracion_avance !== null) {
                $sumaAvance += max(0.0, min(100.0, (float) $reg->seq_valoracion_avance));
            } else {
                $sumaAvance += $reg->seq_cumplido ? 80.0 : 30.0;
            }
        }

        $total = $registros->count();
        return [
            'total_seguimientos' => $total,
            'promedio_avance'    => round($sumaAvance / $total, 1),
            'tasa_cumplimiento'  => round(($cumplidos / $total) * 100, 1),
        ];
    }

    /**
     * Determina el semáforo midiendo la Situación del Proyecto por porcentaje (0-100%):
     * - >= 75%: Conforme (en_ruta)
     * - 50% a 74.9%: Observado (acelerar)
     * - 25% a 49.9%: No conforme (riesgo_alto)
     * - < 25% (o >=4 encuentros negativos consecutivos): No viable (inviable)
     */
    private function determinarSemaforo(float $porcentaje, float $avance, float $asistencia, int $correcciones, array $encuentros, int $semana): string
    {
        $negativos = $encuentros['negativos_seguidos'] ?? 0;

        if ($porcentaje < 25.0 || $negativos >= 4) {
            return 'inviable';
        }
        if ($porcentaje < 50.0) {
            return 'riesgo_alto';
        }
        if ($porcentaje < 75.0) {
            return 'acelerar';
        }

        return 'en_ruta';
    }

    /**
     * Asocia clase de color Bootstrap al semáforo.
     */
    private function colorSemaforo(string $semaforo): string
    {
        return match($semaforo) {
            'conforme', 'en_ruta'         => 'success',
            'observado', 'acelerar'       => 'warning',
            'no_conforme', 'riesgo_alto'  => 'danger',
            'no_viable', 'inviable'       => 'dark',
            default                       => 'secondary',
        };
    }

    /**
     * Redacta mensaje constructivo y diagnóstico en lenguaje humano directo con porcentaje.
     */
    private function generarMensaje(string $semaforo, float $porcentaje, float $avance, float $asistencia, int $correcciones, int $semana): string
    {
        return match($semaforo) {
            'conforme', 'en_ruta' => "🟢 Conforme ({$porcentaje}%): Cumple con los estándares y metas. Registra {$avance}% en entregables y {$asistencia}% de asistencia.",
            'observado', 'acelerar' => "🟡 Observado ({$porcentaje}%): Presenta parámetros menores por mejorar. Avance en entregables {$avance}%" .
                ($correcciones > 0 ? " y {$correcciones} corrección(es) pendiente(s)" : "") .
                " (asistencia: {$asistencia}%).",
            'no_conforme', 'riesgo_alto' => "🔴 No conforme ({$porcentaje}%): Incumplimiento significativo de los parámetros del proyecto ({$avance}% en entregables, {$asistencia}% de asistencia)." .
                ($correcciones > 0 ? " Acumula {$correcciones} compromiso(s) sin solventar." : "") .
                " Se aconseja acordar sesión de tutoría urgente.",
            'no_viable', 'inviable' => "⚫ No viable ({$porcentaje}%): Cierre administrativo por baja factibilidad ({$avance}% de entregables, {$asistencia}% de asistencia).",
            default => "Situación preliminar en {$porcentaje}% (semana {$semana}).",
        };
    }

    /**
     * Evalúa la viabilidad matemática de culminar el proyecto con éxito.
     */
    private function evaluarViabilidad(string $semaforo, float $porcentaje, float $avance, float $asistencia, int $semana): bool
    {
        if ($semaforo === 'no_viable' || $semaforo === 'inviable' || $porcentaje < 25.0) {
            return false;
        }
        return true;
    }

    /**
     * Diagnóstico masivo para los equipos de una sección.
     * Retorna array asociativo [id_equipo => datos_diagnostico].
     */
    public function diagnosticarSeccion(array $idsEquipos, ?int $semanaActual = null): array
    {
        if ($semanaActual === null || $semanaActual <= 0) {
            $semanaActual = $this->calcularSemanaActualPeriodo();
        }

        $resultado = [];
        foreach ($idsEquipos as $idEquipo) {
            $resultado[$idEquipo] = $this->diagnosticarEquipo((int) $idEquipo, $semanaActual);
        }
        return $resultado;
    }
}
