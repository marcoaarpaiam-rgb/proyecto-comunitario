<?php
namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;

class ExpedienteController extends Controller
{
    use AutorizaPorSeccion;

    public function show($id)
    {
        $this->autorizarEquipoId($id);

        $equipo = Equipo::with([
            'seccion.trayecto',
            'seccion.turno',
            'trayecto',
            'integrantes.usuario',
            'historial.tipoEvento',
            'historial' => fn($q) => $q->orderBy('heq_fecha_evento', 'desc'),
            'proyectoComunidad.comunidad',
            'proyectoComunidad.tipoProyecto',
            'proyectoComunidad.modalidad',
            'puntosControl.seguimiento.asistencias.usuario',
            'puntosControl.seguimientos.asistencias.usuario',
            'entregables.tipoEntregable',
            'socializaciones.tipoSocializacion',
            'cartaPresentacion',
            'resultadoProyecto.estadoProyecto',
        ])->findOrFail($id);

        $idProyecto = $equipo->proyectoComunidad?->pco_id;

        // Inicializar automáticamente recorrido y PIN si no existen
        if ($idProyecto) {
            app(\App\Services\EvaluacionTrayectoService::class)->inicializarRecorridoProyecto(
                (int) $idProyecto,
                (int) $equipo->equ_id,
                (int) $equipo->equ_id_tra
            );
        }

        // Evaluaciones semestrales acumuladas
        $evaluaciones = DB::table('recorrido_evaluacion_semestral')
            ->where(function ($q) use ($id, $idProyecto) {
                $q->where('id_equipo', $id);
                if ($idProyecto) {
                    $q->orWhere('id_proyecto', $idProyecto);
                }
            })
            ->orderBy('semestre', 'asc')
            ->get();

        // Acta final de trayecto si existe
        $actaFinal = DB::table('actas_finales_trayecto')
            ->where(function ($q) use ($id, $idProyecto) {
                $q->where('id_equipo', $id);
                if ($idProyecto) {
                    $q->orWhere('id_proyecto', $idProyecto);
                }
            })
            ->first();

        $metricasIndividuales = app(\App\Services\EvaluacionTrayectoService::class)->calcularMetricasIndividualesEquipo(
            (int) $equipo->equ_id,
            $idProyecto ? (int) $idProyecto : null
        );

        $semanaActual = 1;
        $maxSem = $equipo->puntosControl?->max('puc_semana');
        if ($maxSem) {
            $semanaActual = min(18, max(1, (int) $maxSem));
        }
        $diagnostico = app(\App\Services\DiagnosticoProyectoService::class)->diagnosticarEquipo((int) $equipo->equ_id, $semanaActual);

        return Inertia::render('Coordinador/ExpedienteEquipo', [
            'equipo'               => $equipo,
            'evaluaciones'         => $evaluaciones,
            'actaFinal'            => $actaFinal,
            'metricasIndividuales' => $metricasIndividuales,
            'diagnostico'          => $diagnostico,
            'semanaActual'         => $semanaActual,
        ]);
    }
}