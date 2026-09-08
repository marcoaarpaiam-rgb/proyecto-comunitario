<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\ResultadoProyecto;
use App\Models\AsistenciaPuntoControl;
use App\Models\SeguimientoEquipo;
use App\Models\Seccion;
use App\Models\Trayecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index()
    {
        $secciones = Seccion::with(['trayecto','turno','equipos'])->where('sec_status', true)->get();

        $reportePorSeccion = $secciones->map(function ($sec) {
            $equipos = $sec->equipos()->where('equ_status', true)
                ->with(['resultadoProyecto.estadoProyecto'])->get();
            return [
                'seccion'    => $sec->sec_codigo,
                'trayecto'   => $sec->trayecto?->tra_nombre,
                'turno'      => $sec->turno?->tur_nombre,
                'total'      => $equipos->count(),
                'aprobados'  => $equipos->filter(fn($e) =>
                    $e->resultadoProyecto?->estadoProyecto?->epr_nombre === 'Aprobado'
                )->count(),
                'aplazados'  => $equipos->filter(fn($e) =>
                    $e->resultadoProyecto?->estadoProyecto?->epr_nombre === 'Aplazado'
                )->count(),
                'sin_resultado' => $equipos->filter(fn($e) =>
                    !$e->resultadoProyecto
                )->count(),
            ];
        });

        return Inertia::render('Coordinador/Reportes', [
            'secciones'         => $secciones,
            'trayectos'         => Trayecto::where('tra_status', true)->get(),
            'resumenGeneral'    => $this->resumenGeneral(),
            'reportePorSeccion' => $reportePorSeccion,
        ]);
    }

    private function resumenGeneral(): array
    {
        $resultados = ResultadoProyecto::with('estadoProyecto')->get();
        return [
            'total_equipos'    => Equipo::where('equ_status', true)->count(),
            'aprobados'        => $resultados->filter(fn($r) =>
                $r->estadoProyecto?->epr_nombre === 'Aprobado')->count(),
            'aplazados'        => $resultados->filter(fn($r) =>
                $r->estadoProyecto?->epr_nombre === 'Aplazado')->count(),
            'no_presentados'   => $resultados->filter(fn($r) =>
                $r->estadoProyecto?->epr_nombre === 'No Presentado')->count(),
            'sin_resultado'    => Equipo::where('equ_status', true)->count()
                - $resultados->count(),
        ];
    }

    public function solvencia(Request $request)
    {
        $request->validate([
            'sec_id' => 'required|exists:seccion,sec_id',
        ]);

        $seccion = Seccion::with(['trayecto','turno'])->findOrFail($request->sec_id);
        $equipos = Equipo::where('equ_id_sec', $request->sec_id)
            ->where('equ_status', true)
            ->with([
                'integrantes.usuario',
                'seguimientos.puntoControl',
                'seguimientos.asistencias',
                'proyectoComunidad.comunidad',
                'resultadoProyecto.estadoProyecto',
            ])->get();

        $reporte = [];
        foreach ($equipos as $equipo) {
            $integrantes = [];
            foreach ($equipo->integrantes as $integrante) {
                $totalPuntos   = $equipo->seguimientos->count();
                $asistencias   = 0;
                foreach ($equipo->seguimientos as $seq) {
                    $asistio = $seq->asistencias
                        ->where('apc_id_usu', $integrante->ein_id_usu)
                        ->first();
                    if ($asistio && $asistio->apc_asistio) $asistencias++;
                }
                $porcentaje = $totalPuntos > 0
                    ? round($asistencias / $totalPuntos * 100) : 0;

                $integrantes[] = [
                    'nombre'     => $integrante->usuario?->usu_primer_nombre
                        . ' ' . $integrante->usuario?->usu_primer_apellido,
                    'cedula'     => $integrante->usuario?->usu_cedula,
                    'es_lider'   => $integrante->ein_es_lider,
                    'asistencias'=> $asistencias,
                    'total'      => $totalPuntos,
                    'porcentaje' => $porcentaje,
                    'solvente'   => $porcentaje >= 75,
                ];
            }

            $reporte[] = [
                'equipo'     => $equipo->equ_codigo,
                'titulo'     => $equipo->equ_titulo,
                'comunidad'  => $equipo->proyectoComunidad?->comunidad?->com_nombre,
                'resultado'  => $equipo->resultadoProyecto?->estadoProyecto?->epr_nombre,
                'integrantes'=> $integrantes,
            ];
        }

        return Inertia::render('Coordinador/ReporteSolvencia', [
            'seccion' => $seccion,
            'reporte' => $reporte,
        ]);
    }
}