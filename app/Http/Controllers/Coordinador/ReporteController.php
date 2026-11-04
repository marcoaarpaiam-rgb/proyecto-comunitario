<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\ResultadoProyecto;
use App\Models\Seccion;
use App\Models\Trayecto;
use App\Models\Comunidad;
use App\Models\TipoProyecto;
use App\Models\Entregable;
use App\Models\Socializacion;
use App\Models\PuntoControl;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        $secciones   = Seccion::with(['trayecto', 'turno'])->where('sec_status', true)->get();
        $trayectos   = Trayecto::where('tra_status', true)->get();
        $comunidades = Comunidad::where('com_status', true)->orderBy('com_nombre')->get();
        $tiposProyecto = TipoProyecto::where('tpr_status', true)->get();
        $profesores  = Usuario::whereHas('usuarioRoles', fn($q) =>
            $q->where('uro_status', true)
              ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'profesor_proyecto'))
        )->get(['usu_id', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_cedula']);

        return Inertia::render('Coordinador/Reportes', [
            'secciones'         => $secciones,
            'trayectos'         => $trayectos,
            'comunidades'       => $comunidades,
            'tiposProyecto'     => $tiposProyecto,
            'profesores'        => $profesores,
            'resumenGeneral'    => $this->resumenGeneral(),
            'reportePorSeccion' => $this->reportePorSeccion($secciones),
        ]);
    }

    private function resumenGeneral(): array
    {
        $resultados = ResultadoProyecto::with('estadoProyecto')->get();
        $totalEquipos = Equipo::where('equ_status', true)->count();
        return [
            'total_equipos'   => $totalEquipos,
            'aprobados'       => $resultados->filter(fn($r) =>
                $r->estadoProyecto?->epr_nombre === 'Aprobado')->count(),
            'aplazados'       => $resultados->filter(fn($r) =>
                $r->estadoProyecto?->epr_nombre === 'Aplazado')->count(),
            'no_presentados'  => $resultados->filter(fn($r) =>
                $r->estadoProyecto?->epr_nombre === 'No Presentado')->count(),
            'sin_resultado'   => $totalEquipos - $resultados->count(),
            'total_comunidades' => Comunidad::where('com_status', true)->count(),
            'total_entregables' => Entregable::where('entr_status', true)->count(),
            'entregables_aprobados' => Entregable::where('entr_aprobado', true)->count(),
            'socializaciones_aptas' => Socializacion::where('soc_apto', true)->count(),
        ];
    }

    private function reportePorSeccion($secciones): array
    {
        return $secciones->map(function ($sec) {
            $equipos = Equipo::where('equ_id_sec', $sec->sec_id)
                ->where('equ_status', true)
                ->with(['resultadoProyecto.estadoProyecto'])
                ->get();
            return [
                'seccion'      => $sec->sec_codigo,
                'trayecto'     => $sec->trayecto?->tra_nombre,
                'turno'        => $sec->turno?->tur_nombre,
                'total'        => $equipos->count(),
                'aprobados'    => $equipos->filter(fn($e) =>
                    $e->resultadoProyecto?->estadoProyecto?->epr_nombre === 'Aprobado'
                )->count(),
                'aplazados'    => $equipos->filter(fn($e) =>
                    $e->resultadoProyecto?->estadoProyecto?->epr_nombre === 'Aplazado'
                )->count(),
                'sin_resultado'=> $equipos->filter(fn($e) =>
                    !$e->resultadoProyecto
                )->count(),
            ];
        })->toArray();
    }

    // ── Filtros dinámicos ────────────────────────────────────────────
    public function filtrar(Request $request)
    {
        $query = Equipo::where('equ_status', true)
            ->with([
                'seccion.trayecto',
                'seccion.turno',
                'integrantes.usuario',
                'proyectoComunidad.comunidad',
                'proyectoComunidad.tipoProyecto',
                'resultadoProyecto.estadoProyecto',
                'puntosControl',
            ]);

        // Filtro por sección
        if ($request->filled('sec_id')) {
            $query->where('equ_id_sec', $request->sec_id);
        }

        // Filtro por trayecto
        if ($request->filled('tra_id')) {
            $query->where('equ_id_tra', $request->tra_id);
        }

        // Filtro por comunidad
        if ($request->filled('com_id')) {
            $query->whereHas('proyectoComunidad', fn($q) =>
                $q->where('pco_id_com', $request->com_id)
            );
        }

        // Filtro por tipo de proyecto
        if ($request->filled('tpr_id')) {
            $query->whereHas('proyectoComunidad', fn($q) =>
                $q->where('pco_id_tpr', $request->tpr_id)
            );
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            if ($request->estado === 'sin_resultado') {
                $query->whereDoesntHave('resultadoProyecto');
            } else {
                $query->whereHas('resultadoProyecto.estadoProyecto', fn($q) =>
                    $q->where('epr_nombre', $request->estado)
                );
            }
        }

        // Filtro por profesor
        if ($request->filled('prof_id')) {
            $query->whereHas('seccion.profesores', fn($q) =>
                $q->where('pse_id_usu', $request->prof_id)
                  ->where('pse_status', true)
            );
        }

        // Filtro por fecha límite vencida
        if ($request->filled('vencidos')) {
            $query->whereHas('proyectoComunidad', fn($q) => $q->where('pco_fecha_limite', '<', now()->toDateString()))
                ->whereDoesntHave('resultadoProyecto');
        }

        // Filtro por rango de fecha
        if ($request->filled('fecha_desde')) {
            $query->whereHas('proyectoComunidad', fn($q) =>
                $q->where('pco_fecha_limite', '>=', $request->fecha_desde)
            );
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereHas('proyectoComunidad', fn($q) =>
                $q->where('pco_fecha_limite', '<=', $request->fecha_hasta)
            );
        }

        $equipos = $query->get();

        return response()->json([
            'equipos' => $equipos->map(fn($e) => [
                'equ_id'      => $e->equ_id,
                'equ_codigo'  => $e->equ_codigo,
                'equ_titulo'  => $e->equ_titulo,
                'seccion'     => $e->seccion?->sec_codigo,
                'trayecto'    => $e->seccion?->trayecto?->tra_nombre,
                'turno'       => $e->seccion?->turno?->tur_nombre,
                'comunidad'   => $e->proyectoComunidad?->comunidad?->com_nombre,
                'tipo_proyecto' => $e->proyectoComunidad?->tipoProyecto?->tpr_nombre,
                'fecha_limite'  => $e->proyectoComunidad?->pco_fecha_limite,
                'resultado'   => $e->resultadoProyecto?->estadoProyecto?->epr_nombre ?? 'Sin resultado',
                'total_puntos'=> $e->puntosControl?->count() ?? 0,
            ]),
            'total' => $equipos->count(),
        ]);
    }

    // ── Reporte por comunidad ────────────────────────────────────────
    public function porComunidad()
    {
        $comunidades = Comunidad::where('com_status', true)
            ->withCount(['proyectosComunidad as total_equipos' => fn($q) =>
                $q->whereHas('equipo', fn($q2) => $q2->where('equ_status', true))
            ])
            ->with(['proyectosComunidad' => fn($q) =>
                $q->whereHas('equipo', fn($q2) => $q2->where('equ_status', true))
                  ->with([
                      'equipo.resultadoProyecto.estadoProyecto',
                      'tipoProyecto',
                      'beneficiarios',
                  ])
            ])
            ->orderBy('com_nombre')
            ->get();

        return response()->json([
            'comunidades' => $comunidades->map(fn($c) => [
                'com_id'       => $c->com_id,
                'com_nombre'   => $c->com_nombre,
                'com_ubicacion'=> $c->com_ubicacion,
                'total_equipos'=> $c->total_equipos,
                'proyectos'    => $c->proyectosComunidad->map(fn($p) => [
                    'codigo'         => $p->equipo?->equ_codigo,
                    'titulo'         => $p->equipo?->equ_titulo,
                    'tipo_proyecto'  => $p->tipoProyecto?->tpr_nombre,
                    'resultado'      => $p->equipo?->resultadoProyecto?->estadoProyecto?->epr_nombre ?? 'En proceso',
                    'total_beneficiarios' => $p->beneficiarios?->sum('pbe_cantidad') ?? 0,
                ]),
            ]),
        ]);
    }

    // ── Reporte de entregables ───────────────────────────────────────
    public function porEntregables(Request $request)
    {
        $query = Entregable::with(['equipo.seccion', 'tipoEntregable'])
            ->where('entr_status', true);

        if ($request->filled('sec_id')) {
            $query->whereHas('equipo', fn($q) => $q->where('equ_id_sec', $request->sec_id));
        }

        $entregables = $query->get();

        return response()->json([
            'total'     => $entregables->count(),
            'aprobados' => $entregables->where('entr_aprobado', true)->count(),
            'rechazados'=> $entregables->where('entr_aprobado', false)->count(),
            'pendientes'=> $entregables->whereNull('entr_aprobado')->count(),
            'detalle'   => $entregables->map(fn($e) => [
                'equipo'   => $e->equipo?->equ_codigo,
                'seccion'  => $e->equipo?->seccion?->sec_codigo,
                'tipo'     => $e->tipoEntregable?->tet_nombre,
                'archivo'  => $e->entr_nombre_archivo,
                'estado'   => $e->entr_aprobado === null ? 'Pendiente'
                    : ($e->entr_aprobado ? 'Aprobado' : 'Rechazado'),
                'fecha'    => $e->entr_fecha_subida,
            ]),
        ]);
    }

    // ── Reporte de puntos de control ─────────────────────────────────
    public function porPuntosControl(Request $request)
    {
        $query = PuntoControl::with(['equipo.seccion', 'seguimientos'])
            ->where('puc_status', true);

        if ($request->filled('sec_id')) {
            $query->whereHas('equipo', fn($q) => $q->where('equ_id_sec', $request->sec_id));
        }

        $puntos = $query->get();

        return response()->json([
            'total'    => $puntos->count(),
            'cumplidos'=> $puntos->filter(fn($p) => $p->seguimientos->where('seq_cumplido', true)->count() > 0)->count(),
            'vencidos' => $puntos->filter(fn($p) =>
                $p->seguimientos->isEmpty() && $p->puc_fecha_limite < now()->toDateString()
            )->count(),
            'pendientes'=> $puntos->filter(fn($p) =>
                $p->seguimientos->isEmpty() && $p->puc_fecha_limite >= now()->toDateString()
            )->count(),
        ]);
    }

    // ── Solvencia ────────────────────────────────────────────────────
    public function solvencia(Request $request)
    {
        $request->validate(['sec_id' => 'required|exists:seccion,sec_id']);

        $seccion = Seccion::with(['trayecto', 'turno'])->findOrFail($request->sec_id);
        $equipos = Equipo::where('equ_id_sec', $request->sec_id)
            ->where('equ_status', true)
            ->with([
                'integrantes.usuario',
                'puntosControl.seguimientos.asistencias.usuario',
                'proyectoComunidad.comunidad',
                'resultadoProyecto.estadoProyecto',
            ])->get();

        $reporte = $equipos->map(function ($equipo) {
            $todosLosSeguimientos = $equipo->puntosControl
                ->flatMap(fn($p) => $p->seguimientos);
            $totalPuntos = $todosLosSeguimientos->count();

            $integrantes = $equipo->integrantes->map(function ($integrante) use ($todosLosSeguimientos, $totalPuntos) {
                $asistencias = 0;
                foreach ($todosLosSeguimientos as $seq) {
                    $asistio = $seq->asistencias
                        ->where('apc_id_usu', $integrante->ein_id_usu)
                        ->first();
                    if ($asistio && $asistio->apc_asistio) $asistencias++;
                }
                $porcentaje = $totalPuntos > 0
                    ? round($asistencias / $totalPuntos * 100) : 0;

                return [
                    'nombre'     => $integrante->usuario?->usu_primer_nombre
                        . ' ' . $integrante->usuario?->usu_primer_apellido,
                    'cedula'     => $integrante->usuario?->usu_cedula,
                    'es_lider'   => $integrante->ein_es_lider,
                    'asistencias'=> $asistencias,
                    'total'      => $totalPuntos,
                    'porcentaje' => $porcentaje,
                    'solvente'   => $porcentaje >= 75,
                ];
            })->toArray();

            return [
                'equipo'      => $equipo->equ_codigo,
                'titulo'      => $equipo->equ_titulo,
                'comunidad'   => $equipo->proyectoComunidad?->comunidad?->com_nombre,
                'resultado'   => $equipo->resultadoProyecto?->estadoProyecto?->epr_nombre,
                'integrantes' => $integrantes,
            ];
        })->toArray();

        return Inertia::render('Coordinador/ReporteSolvencia', [
            'seccion' => $seccion,
            'reporte' => $reporte,
        ]);
    }

    public function exportarSolvenciaPdf(Request $request)
    {
        $request->validate(['sec_id' => 'required|exists:seccion,sec_id']);

        $seccion = Seccion::with(['trayecto', 'turno'])->findOrFail($request->sec_id);
        $equipos = Equipo::where('equ_id_sec', $request->sec_id)
            ->where('equ_status', true)
            ->with([
                'integrantes.usuario',
                'puntosControl.seguimientos.asistencias',
                'proyectoComunidad.comunidad',
                'resultadoProyecto.estadoProyecto',
            ])->get();

        $reporte = $equipos->map(function ($equipo) {
            $todosLosSeguimientos = $equipo->puntosControl
                ->flatMap(fn($p) => $p->seguimientos);
            $totalPuntos = $todosLosSeguimientos->count();

            $integrantes = $equipo->integrantes->map(function ($integrante) use ($todosLosSeguimientos, $totalPuntos) {
                $asistencias = 0;
                foreach ($todosLosSeguimientos as $seq) {
                    $asistio = $seq->asistencias
                        ->where('apc_id_usu', $integrante->ein_id_usu)->first();
                    if ($asistio && $asistio->apc_asistio) $asistencias++;
                }
                $porcentaje = $totalPuntos > 0
                    ? round($asistencias / $totalPuntos * 100) : 0;
                return [
                    'nombre'     => $integrante->usuario?->usu_primer_nombre
                        . ' ' . $integrante->usuario?->usu_primer_apellido,
                    'cedula'     => $integrante->usuario?->usu_cedula,
                    'es_lider'   => $integrante->ein_es_lider,
                    'asistencias'=> $asistencias,
                    'total'      => $totalPuntos,
                    'porcentaje' => $porcentaje,
                    'solvente'   => $porcentaje >= 75,
                ];
            })->toArray();

            return [
                'equipo'      => $equipo->equ_codigo,
                'titulo'      => $equipo->equ_titulo,
                'comunidad'   => $equipo->proyectoComunidad?->comunidad?->com_nombre,
                'resultado'   => $equipo->resultadoProyecto?->estadoProyecto?->epr_nombre,
                'integrantes' => $integrantes,
            ];
        })->toArray();

        $pdf = Pdf::loadView('pdf.reporte_solvencia', compact('seccion', 'reporte'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("Solvencia_{$seccion->sec_codigo}.pdf");
    }

    public function exportarGeneralPdf()
    {
        $secciones = Seccion::with(['trayecto', 'turno', 'equipos.resultadoProyecto.estadoProyecto'])
            ->where('sec_status', true)->get();
        $resumen = $this->resumenGeneral();
        $pdf = Pdf::loadView('pdf.reporte_general', compact('secciones', 'resumen'))
            ->setPaper('letter', 'landscape');
        return $pdf->download('Reporte_General_' . now()->format('Y-m-d') . '.pdf');
    }
}