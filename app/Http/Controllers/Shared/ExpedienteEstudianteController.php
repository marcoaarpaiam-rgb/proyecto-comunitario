<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\EquipoIntegrante;
use App\Models\HistorialEquipo;
use App\Models\AsistenciaPuntoControl;
use App\Models\IntegranteSocializacion;
use App\Models\EstudianteSeccion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpedienteEstudianteController extends Controller
{
    /**
     * Búsqueda rápida de estudiantes por Cédula, Nombre o Apellido
     */
    public function buscar(Request $request)
    {
        $q = trim($request->input('q', ''));
        $usuarioAutenticado = auth()->user();
        $esProfesor = $usuarioAutenticado && $usuarioAutenticado->hasRol('profesor_proyecto');
        $esCoordinador = $usuarioAutenticado && $usuarioAutenticado->hasRol('coordinador');

        $query = Usuario::where('usu_status', true);

        if (strlen($q) >= 2) {
            $query->where(function ($query) use ($q) {
                $query->where('usu_cedula', 'like', "%{$q}%")
                      ->orWhere('usu_primer_nombre', 'like', "%{$q}%")
                      ->orWhere('usu_primer_apellido', 'like', "%{$q}%")
                      ->orWhere('usu_usuario', 'like', "%{$q}%");
            });
        }

        $query->whereHas('usuarioRoles', fn($r) => $r->whereHas('rol', fn($rol) => $rol->whereIn('rol_nombre', ['lider', 'estudiante'])));

        // Si es profesor, restringir la búsqueda únicamente a estudiantes de sus secciones asignadas
        if ($esProfesor) {
            $misSeccionesIds = \App\Models\ProfesorSeccion::where('pse_id_usu', $usuarioAutenticado->usu_id)
                ->where('pse_status', true)
                ->pluck('pse_id_sec');

            $query->whereHas('seccionesEstudiante', function ($se) use ($misSeccionesIds) {
                $se->where('ese_status', true)
                   ->whereIn('ese_id_sec', $misSeccionesIds);
            });
        }

        $estudiantes = $query->with([
                'seccionesEstudiante.seccion.trayecto',
                'equiposIntegrante.equipo',
            ])
            ->take(25)
            ->get(['usu_id', 'usu_cedula', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_email', 'usu_telefono']);

        // Si la petición espera JSON explícito (ej: autocompletado con axios)
        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($estudiantes);
        }

        // Si es una navegación directa de Inertia a /estudiantes/buscar
        // Redirigir limpiamente a la ruta del primer estudiante encontrado
        $primerEstudianteId = $estudiantes->first()?->usu_id;
        if ($primerEstudianteId) {
            $rutaExpediente = $esCoordinador ? 'coordinador.estudiantes.expediente' : 'profesor.estudiantes.expediente';
            return redirect()->route($rutaExpediente, $primerEstudianteId);
        }

        return Inertia::render('Shared/ExpedienteEstudiante', [
            'estudiante'        => null,
            'historialEventos'  => [],
            'asistenciasPuntos' => [],
            'socializaciones'   => [],
            'backUrl'           => $esCoordinador ? '/coordinador' : '/profesor',
            'esCoordinador'     => (bool)$esCoordinador,
            'estudiantesLista'  => $estudiantes,
        ]);
    }

    /**
     * Ficha y Expediente Completo del Estudiante
     */
    public function show($id)
    {
        $usuarioAutenticado = auth()->user();
        $esProfesor = $usuarioAutenticado && $usuarioAutenticado->hasRol('profesor_proyecto');

        if ($esProfesor) {
            $misSeccionesIds = \App\Models\ProfesorSeccion::where('pse_id_usu', $usuarioAutenticado->usu_id)
                ->where('pse_status', true)
                ->pluck('pse_id_sec');

            $pertenece = EstudianteSeccion::where('ese_id_usu', $id)
                ->where('ese_status', true)
                ->whereIn('ese_id_sec', $misSeccionesIds)
                ->exists();

            if (!$pertenece) {
                abort(403, 'No tienes autorización para consultar el expediente de este estudiante ya que no pertenece a tus secciones asignadas.');
            }
        }
        $estudiante = Usuario::with([
            'seccionesEstudiante.seccion.trayecto',
            'seccionesEstudiante.seccion.turno',
            'equiposIntegrante.equipo.trayecto',
            'equiposIntegrante.equipo.seccion',
            'equiposIntegrante.equipo.proyectoComunidad.comunidad',
        ])->findOrFail($id);

        // Historial de eventos en equipos (entradas, salidas, disoluciones, reactivaciones)
        $equiposIds = $estudiante->equiposIntegrante->pluck('ein_id_equ')->filter()->unique();

        $historialEventos = HistorialEquipo::where(function ($q) use ($equiposIds, $estudiante) {
                if ($equiposIds->isNotEmpty()) {
                    $q->whereIn('heq_id_equ', $equiposIds);
                }
                $q->orWhere('heq_motivo', 'like', "%{$estudiante->usu_cedula}%")
                  ->orWhere('heq_motivo', 'like', "%{$estudiante->usu_primer_nombre}%");
            })
            ->with(['equipo', 'tipoEvento', 'usuarioModifico'])
            ->orderBy('heq_fecha_evento', 'desc')
            ->get()
            ->map(function ($heq) {
                return [
                    'heq_id'           => $heq->heq_id,
                    'heq_fecha_evento' => $heq->heq_fecha_evento,
                    'heq_motivo'       => $heq->heq_motivo,
                    'heq_detalles'     => $heq->heq_motivo,
                    'heq_id_equ'       => $heq->heq_id_equ,
                    'equipo'           => $heq->equipo,
                    'tipo_evento'      => [
                        'tee_nombre' => $heq->tipoEvento?->tee_nombre,
                        'tev_nombre' => $heq->tipoEvento?->tee_nombre,
                    ],
                    'usuario_modifico' => $heq->usuarioModifico,
                ];
            });

        // Asistencias y observaciones en Puntos de Control
        $asistenciasPuntos = AsistenciaPuntoControl::where('apc_id_usu', $id)
            ->with([
                'seguimiento.puntoControl.equipo',
                'seguimiento.puntoControl.trayecto',
            ])
            ->get()
            ->map(function ($asist) {
                $seq = $asist->seguimiento;
                $puc = $seq?->puntoControl;
                return [
                    'apc_id'            => $asist->apc_id,
                    'puc_nombre'        => $puc?->puc_nombre ?? 'Punto de Control',
                    'puc_semana'        => $puc?->puc_semana ?? 6,
                    'fecha_limite'      => $puc?->puc_fecha_limite,
                    'fecha_registro'    => $seq?->seq_fecha_registro,
                    'equipo_codigo'     => $puc?->equipo?->equ_codigo,
                    'equipo_titulo'     => $puc?->equipo?->equ_titulo,
                    'asistio'           => (bool)$asist->apc_asistio,
                    'justificado'       => (bool)$asist->apc_justificado,
                    'justificativo'     => $asist->apc_justificativo,
                    'observacion_indiv' => $asist->apc_observaciones,
                    'obs_generales'     => $seq?->seq_observaciones,
                    'cumplio_previo'    => $seq?->seq_cumplio_previo,
                    'verif_previo'      => $seq?->seq_obs_previas_verificacion,
                ];
            });

        // Participación en Pre-Socializaciones y Socializaciones Finales
        $socializaciones = IntegranteSocializacion::where('iso_id_usu', $id)
            ->with([
                'socializacion.equipo',
                'socializacion.tipoSocializacion',
                'socializacion.jurados.tipoJurado',
            ])
            ->get()
            ->map(function ($iso) {
                $soc = $iso->socializacion;
                return [
                    'iso_id'          => $iso->iso_id,
                    'tipo'            => $soc?->tipoSocializacion?->tso_nombre ?? 'Socialización',
                    'orden'           => $soc?->tipoSocializacion?->tso_orden,
                    'fecha'           => $soc?->soc_fecha,
                    'lugar'           => $soc?->soc_lugar,
                    'equipo_codigo'   => $soc?->equipo?->equ_codigo,
                    'asistio'         => (bool)$iso->iso_asistio,
                    'avanza'          => (bool)$iso->iso_avanza,
                    'justificacion'   => $iso->iso_justificacion,
                    'apto_equipo'     => $soc?->soc_apto,
                    'aprobado_equipo' => $soc?->soc_aprobado,
                    'observaciones'   => $soc?->soc_observaciones,
                ];
            });

        // Rol actual en el sistema
        $esCoordinador = auth()->user()->hasRol('coordinador');
        $backUrl = $esCoordinador ? '/coordinador' : '/profesor';

        return Inertia::render('Shared/ExpedienteEstudiante', [
            'estudiante'        => $estudiante,
            'historialEventos'  => $historialEventos,
            'asistenciasPuntos' => $asistenciasPuntos,
            'socializaciones'   => $socializaciones,
            'backUrl'           => $backUrl,
            'esCoordinador'     => $esCoordinador,
        ]);
    }
}
