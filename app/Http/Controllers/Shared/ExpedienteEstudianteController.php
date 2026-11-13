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
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $estudiantes = Usuario::where('usu_status', true)
            ->where(function ($query) use ($q) {
                $query->where('usu_cedula', 'like', "%{$q}%")
                      ->orWhere('usu_primer_nombre', 'like', "%{$q}%")
                      ->orWhere('usu_primer_apellido', 'like', "%{$q}%")
                      ->orWhere('usu_usuario', 'like', "%{$q}%");
            })
            ->whereHas('usuarioRoles', fn($r) => $r->whereHas('rol', fn($rol) => $rol->whereIn('rol_nombre', ['lider', 'estudiante'])))
            ->with([
                'seccionesEstudiante.seccion.trayecto',
                'equiposIntegrante.equipo',
            ])
            ->take(15)
            ->get(['usu_id', 'usu_cedula', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_correo', 'usu_telefono']);

        return response()->json($estudiantes);
    }

    /**
     * Ficha y Expediente Completo del Estudiante
     */
    public function show($id)
    {
        $estudiante = Usuario::with([
            'seccionesEstudiante.seccion.trayecto',
            'seccionesEstudiante.seccion.turno',
            'equiposIntegrante.equipo.trayecto',
            'equiposIntegrante.equipo.seccion',
            'equiposIntegrante.equipo.proyectoComunidad.comunidad',
        ])->findOrFail($id);

        // Historial de eventos en equipos (entradas, salidas, expulsiones, disoluciones)
        $historialEventos = HistorialEquipo::where('heq_id_usu', $id)
            ->orWhere(function ($q) use ($estudiante) {
                $q->where('heq_detalles', 'like', "%{$estudiante->usu_cedula}%")
                  ->orWhere('heq_detalles', 'like', "%{$estudiante->usu_primer_nombre}%");
            })
            ->with(['equipo', 'tipoEvento', 'usuarioModifico'])
            ->orderBy('heq_fecha_evento', 'desc')
            ->get();

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
