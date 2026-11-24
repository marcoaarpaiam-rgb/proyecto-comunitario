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
        $semaforo          = $this->determinarSemaforo($avanceEntregables, $asistencia, $correccionesPend, $estadoEncuentros, $semanaActual);
        $mensaje           = $this->generarMensaje($semaforo, $avanceEntregables, $asistencia, $correccionesPend, $semanaActual);
        $viable            = $this->evaluarViabilidad($semaforo, $avanceEntregables, $asistencia, $semanaActual);

        return [
            'id_equipo'                    => $idEquipo,
            'codigo_equipo'                => $codigo,
            'titulo_equipo'                => $titulo,
            'nombre_equipo'                => $nombreEquipo,
            'semestre'                     => $semestre,
            'semaforo'                     => $semaforo,              // 'en_ruta' | 'acelerar' | 'riesgo_alto' | 'inviable'
            'color'                        => $this->colorSemaforo($semaforo),
            'avance_entregables'           => $avanceEntregables,     // float 0-100 (aprobados)
            'avance_subidos'               => $statsEntregables['porcentaje_subidos'] ?? 0.0,
            'entregables_stats'            => $statsEntregables,
            'asistencia_promedio'          => $asistencia,            // float 0-100
            'correcciones_pendientes'      => $correccionesPend,      // int
            'encuentros_negativos_seguidos' => $estadoEncuentros['negativos_seguidos'],
            'ultimos_encuentros'           => $estadoEncuentros['ultimos'],
            'mensaje'                      => $mensaje,
            'es_viable'                    => $viable,
            'semana_actual'                => $semanaActual,
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
     * Analiza el historial reciente de valoraciones de encuentros.
     */
    private function analizarEncuentros(int $idEquipo): array
    {
        if (!Schema::hasTable('seguimiento_equipo') || !Schema::hasColumn('seguimiento_equipo', 'seq_estado_semaforo')) {
            return ['negativos_seguidos' => 0, 'ultimos' => []];
        }

        try {
            $ultimos = DB::table('seguimiento_equipo')
                ->where('seq_id_equ', $idEquipo)
                ->whereNotNull('seq_estado_semaforo')
                ->orderBy('seq_id', 'desc')
                ->take(5)
                ->pluck('seq_estado_semaforo')
                ->toArray();

            $negativos = 0;
            foreach ($ultimos as $estado) {
                if ($estado === 'negativo') {
                    $negativos++;
                } else {
                    break;
                }
            }

            return ['negativos_seguidos' => $negativos, 'ultimos' => $ultimos];
        } catch (\Exception $e) {
            return ['negativos_seguidos' => 0, 'ultimos' => []];
        }
    }

    /**
     * Determina el semáforo considerando la semana del período y la ruta crítica.
     */
    private function determinarSemaforo(float $avance, float $asistencia, int $correcciones, array $encuentros, int $semana): string
    {
        $negativos = $encuentros['negativos_seguidos'] ?? 0;

        // Criterios de inviabilidad (recta final semana >= 15 con avance nulo o múltiples encuentros negativos consecutivos)
        if ($semana >= 15 && $avance < 25 && $asistencia < 60) {
            return 'inviable';
        }
        if ($negativos >= 4) {
            return 'inviable';
        }

        // Riesgo alto
        if ($asistencia < 65) {
            return 'riesgo_alto';
        }
        if ($semana >= 10 && $avance < 30) {
            return 'riesgo_alto';
        }
        if ($semana >= 14 && $avance < 60) {
            return 'riesgo_alto';
        }
        if ($negativos >= 2 || $correcciones >= 3) {
            return 'riesgo_alto';
        }

        // Acelerar (alertas preventivas según la fase del semestre)
        if ($semana >= 7 && $avance < 40) {
            return 'acelerar';
        }
        if ($semana >= 12 && $avance < 70) {
            return 'acelerar';
        }
        if ($asistencia < 80) {
            return 'acelerar';
        }
        if ($correcciones >= 2) {
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
            'en_ruta'    => 'success',
            'acelerar'   => 'warning',
            'riesgo_alto'=> 'danger',
            'inviable'   => 'dark',
            default      => 'secondary',
        };
    }

    /**
     * Redacta mensaje constructivo y diagnóstico en lenguaje humano directo.
     */
    private function generarMensaje(string $semaforo, float $avance, float $asistencia, int $correcciones, int $semana): string
    {
        return match($semaforo) {
            'en_ruta' => "✅ El equipo avanza a buen ritmo. Registra {$avance}% de entregables aprobados y {$asistencia}% de asistencia. ¡Excelente progreso en semana {$semana}!",
            'acelerar' => "⚠️ Semana {$semana}: El equipo necesita acelerar el paso. Avance de entregables en {$avance}%." .
                ($correcciones > 0 ? " Mantiene {$correcciones} corrección(es) pendiente(s)." : "") .
                " Asistencia promedio: {$asistencia}%.",
            'riesgo_alto' => "🔴 ALERTA en semana {$semana}: El equipo está en riesgo crítico. Avance de apenas {$avance}% y asistencia de {$asistencia}%." .
                ($correcciones > 0 ? " Acumula {$correcciones} compromiso(s) sin solventar." : "") .
                " Se aconseja acordar sesión de tutoría urgente.",
            'inviable' => "⛔ ATENCIÓN - Semana {$semana}: Con {$avance}% de avance y {$asistencia}% de asistencia, matemáticamente el cumplimiento del trayecto está comprometido. Es imprescindible evaluar plan de recuperación o aplazamiento formal.",
            default => "Información preliminar en semana {$semana}.",
        };
    }

    /**
     * Evalúa la viabilidad matemática de culminar el proyecto con éxito.
     */
    private function evaluarViabilidad(string $semaforo, float $avance, float $asistencia, int $semana): bool
    {
        if ($semaforo === 'inviable') {
            return false;
        }
        if ($semana >= 15 && $avance < 20) {
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
