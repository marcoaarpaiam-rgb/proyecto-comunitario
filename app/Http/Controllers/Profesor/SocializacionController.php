<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Socializacion;
use App\Models\JuradoSocializacion;
use App\Models\IntegranteSocializacion;
use App\Models\EvidenciaSocializacion;
use App\Models\TipoSocializacion;
use App\Models\Equipo;
use App\Models\TipoJurado;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;

class SocializacionController extends Controller
{
    use AutorizaPorSeccion;
    public function index()
    {
        $profesor = auth()->user();
        $profSecciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->with(['seccion.trayecto', 'seccion.turno'])
            ->get();
        $seccionesIds = $profSecciones->pluck('pse_id_sec');

        $equipos = Equipo::whereIn('equ_id_sec', $seccionesIds)
            ->where('equ_status', true)
            ->with(['trayecto', 'seccion', 'integrantes.usuario'])
            ->get();

        return Inertia::render('Profesor/Socializaciones', [
            'socializaciones' => Socializacion::whereIn('soc_id_equ', $equipos->pluck('equ_id'))
                ->with([
                    'equipo.seccion', 'trayecto', 'tipoSocializacion',
                    'jurados', 'integrantes.usuario',
                    'socializacionPrevia',
                ])
                ->orderBy('soc_fecha', 'desc')
                ->get(),
            'equipos'          => $equipos,
            'secciones'        => $profSecciones->map(fn($ps) => $ps->seccion)->filter()->values(),
            'tiposSocializacion' => TipoSocializacion::orderBy('tso_orden')->get(),
            'tiposJurado'      => TipoJurado::where('tju_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'soc_id_equ'  => 'required|exists:equipo,equ_id',
            'soc_id_tso'  => 'required|exists:tipo_socializacion,tso_id',
            'soc_fecha'   => 'required|date',
            'soc_hora'    => 'required',
            'soc_lugar'   => 'required|string|max:150',
            'jurados'     => 'sometimes|array',
            'integrantes' => 'sometimes|array',
        ], [
            'soc_id_equ.required' => 'El equipo es obligatorio.',
            'soc_id_tso.required' => 'El tipo de socialización es obligatorio.',
            'soc_fecha.required'  => 'La fecha es obligatoria.',
            'soc_hora.required'   => 'La hora es obligatoria.',
            'soc_lugar.required'  => 'El lugar es obligatorio.',
        ]);
        
        $this->autorizarEquipoId($request->soc_id_equ);

        // Obtener trayecto del equipo
        $equipo = Equipo::findOrFail($request->soc_id_equ);
        
        // Verificar flujo de socializaciones
        $tipoSoc = \App\Models\TipoSocializacion::findOrFail($request->soc_id_tso);

        if ($tipoSoc->tso_orden === 2) {
            // Es socialización final — verificar que existe pre-soc aprobada (apta)
            $preSocAprobada = Socializacion::where('soc_id_equ', $request->soc_id_equ)
                ->whereHas('tipoSocializacion', fn($q) => $q->where('tso_orden', 1))
                ->where('soc_apto', true)
                ->exists();

            if (!$preSocAprobada) {
                return back()->withErrors([
                    'soc_id_tso' => 'No se puede registrar la Socialización Final sin una Pre-Socialización marcada como apta.'
                ]);
            }
        }

        // Verificar que no exista ya una soc del mismo tipo para este equipo
        $yaExiste = Socializacion::where('soc_id_equ', $request->soc_id_equ)
            ->where('soc_id_tso', $request->soc_id_tso)
            ->where('soc_status', true)
            ->exists();

        if ($yaExiste) {
            return back()->withErrors([
                'soc_id_tso' => 'Ya existe una ' . $tipoSoc->tso_nombre . ' registrada para este equipo.'
            ]);
        }

        $soc = Socializacion::create([
            'soc_id_equ'           => $request->soc_id_equ,
            'soc_id_tra'           => $equipo->equ_id_tra,
            'soc_id_tso'           => $request->soc_id_tso,
            'soc_id_soc_previa'    => $request->soc_id_soc_previa,
            'soc_fecha'            => $request->soc_fecha,
            'soc_hora'             => $request->soc_hora,
            'soc_lugar'            => $request->soc_lugar,
            'soc_cantidad_jurados' => count($request->jurados ?? []),
            'soc_status'           => true,
            'soc_id_usu_created'   => auth()->id(),
        ]);

        // Registrar jurados
        foreach ($request->jurados ?? [] as $jurado) {
            JuradoSocializacion::create([
                'jso_id_soc'         => $soc->soc_id,
                'jso_id_usu'         => $jurado['usu_id'] ?? null,
                'jso_nombre_externo' => $jurado['nombre_externo'] ?? null,
                'jso_id_tju'         => $jurado['tju_id'] ?? null,
                'jso_es_de_comunidad'=> $jurado['es_de_comunidad'] ?? false,
                'jso_id_usu_created' => auth()->id(),
            ]);
        }

        // Registrar integrantes
        foreach ($request->integrantes ?? [] as $int) {
            IntegranteSocializacion::create([
                'iso_id_soc'         => $soc->soc_id,
                'iso_id_usu'         => $int['usu_id'],
                'iso_avanza'         => $int['avanza'] ?? null,
                'iso_justificacion'  => $int['justificacion'] ?? null,
                'iso_id_usu_created' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Socialización registrada correctamente.');
    }

    public function registrarResultado(Request $request, $id)
    {
        $request->validate([
            'soc_apto'                  => 'nullable|boolean',
            'soc_aprobado'             => 'nullable|boolean',
            'soc_observaciones_no_apto'=> 'nullable|string',
            'soc_observaciones'        => 'nullable|string',
        ]);

        $soc = Socializacion::with(['tipoSocializacion', 'equipo'])->findOrFail($id);

        $this->autorizarEquipoId($soc->soc_id_equ);
        
        $soc->update([
            'soc_apto'                   => $request->soc_apto,
            'soc_aprobado'              => $request->soc_aprobado,
            'soc_observaciones_no_apto' => $request->soc_observaciones_no_apto,
            'soc_observaciones'         => $request->soc_observaciones,
            'soc_id_usu_updated'        => auth()->id(),
        ]);

        // Actualizar avance de integrantes
        foreach ($request->integrantes ?? [] as $int) {
            IntegranteSocializacion::updateOrCreate(
                ['iso_id_soc' => $soc->soc_id, 'iso_id_usu' => $int['usu_id']],
                [
                    'iso_avanza'        => $int['avanza'],
                    'iso_justificacion' => $int['justificacion'] ?? null,
                ]
            );
        }

        // Si es socialización final y fue aprobada, procesar resultado de proyecto automáticamente
        if ($request->soc_aprobado !== null) {
            $proyecto = \App\Models\ProyectoComunidad::where('pco_id_equ', $soc->soc_id_equ)
                ->where('pco_status', true)->first();

            if ($proyecto) {
                $estadoNombre = $request->soc_aprobado ? 'Aprobado' : 'Aplazado';
                $estadoId = \App\Models\EstadoProyecto::where('epr_nombre', $estadoNombre)->value('epr_id')
                    ?? ($request->soc_aprobado ? 1 : 2);

                \App\Models\ResultadoProyecto::updateOrCreate(
                    ['rpr_id_pco' => $proyecto->pco_id],
                    [
                        'rpr_id_tra'         => $soc->soc_id_tra ?? $soc->equipo?->equ_id_tra,
                        'rpr_id_equ'         => $soc->soc_id_equ,
                        'rpr_id_epr'         => $estadoId,
                        'rpr_observaciones'  => $request->soc_observaciones,
                        'rpr_fecha_registro' => now()->toDateString(),
                        'rpr_status'         => true,
                        'rpr_id_usu_created' => auth()->id(),
                    ]
                );
            }
        }

        // Notificar al líder del equipo
        $lider = \App\Models\EquipoIntegrante::where('ein_id_equ', $soc->soc_id_equ)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();

        if ($lider) {
            $msg = '';
            if ($soc->tipoSocializacion?->tso_orden === 1) {
                $msg = $request->soc_apto 
                    ? "Tu equipo fue calificado como APTO en la Pre-Socialización." 
                    : "Tu equipo fue calificado como NO APTO en la Pre-Socialización.";
            } else {
                $msg = $request->soc_aprobado 
                    ? "¡Felicitaciones! El proyecto de tu equipo fue APROBADO en la Socialización Final." 
                    : "El proyecto de tu equipo fue calificado como NO APROBADO en la Socialización Final.";
            }

            if ($msg) {
                \App\Models\Notificacion::create([
                    'not_id_usu'           => $lider->ein_id_usu,
                    'not_id_tno'           => \App\Models\TipoNotificacion::first()?->tno_id,
                    'not_id_equ'           => $soc->soc_id_equ,
                    'not_mensaje'          => $msg,
                    'not_leida'            => false,
                    'not_fecha_generacion' => now(),
                ]);
            }
        }

        return back()->with('success', 'Resultado registrado correctamente.');
    }
}