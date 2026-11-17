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
                    'equipo.seccion', 'equipo.proyectoComunidad', 'trayecto', 'tipoSocializacion',
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
            'soc_fecha'   => 'required|date|after_or_equal:today',
            'soc_hora'    => 'required',
            'soc_lugar'   => 'required|string|max:150',
            'jurados'     => 'sometimes|array',
            'integrantes' => 'sometimes|array',
        ], [
            'soc_id_equ.required'       => 'El equipo es obligatorio.',
            'soc_id_tso.required'       => 'El tipo de socialización es obligatorio.',
            'soc_fecha.required'        => 'La fecha es obligatoria.',
            'soc_fecha.after_or_equal'  => 'La fecha de la socialización no puede ser anterior a hoy.',
            'soc_hora.required'         => 'La hora es obligatoria.',
            'soc_lugar.required'        => 'El lugar es obligatorio.',
        ]);
        
        $this->autorizarEquipoId($request->soc_id_equ);

        // Obtener trayecto del equipo
        $equipo = Equipo::findOrFail($request->soc_id_equ);
        
        // Verificar flujo de socializaciones
        $tipoSoc = \App\Models\TipoSocializacion::findOrFail($request->soc_id_tso);

        // Semestre impar: cualitativo, NO cuenta con Socialización Final
        $seccion = $equipo->seccion;
        $numSemestre = $seccion ? $seccion->getSemestreNumero() : $equipo->getSemestreNumero();
        if ($numSemestre % 2 !== 0 && $tipoSoc->tso_orden === 2) {
            return back()->withErrors([
                'soc_id_tso' => "El semestre impar (Semestre {$numSemestre}) es cualitativo y no cuenta con Socialización Final con jurados. La Socialización Final se realiza en el semestre par."
            ]);
        }

        if ($tipoSoc->tso_orden === 2) {
            // Es socialización final — verificar que existe pre-soc aprobada (apta)
            $preSocAprobada = Socializacion::where('soc_id_equ', $request->soc_id_equ)
                ->whereHas('tipoSocializacion', fn($q) => $q->where('tso_orden', 1))
                ->where('soc_apto', true)
                ->latest('soc_fecha')
                ->first();

            if (!$preSocAprobada) {
                return back()->withErrors([
                    'soc_id_tso' => 'No se puede registrar la Socialización Final sin una Pre-Socialización marcada como apta.'
                ]);
            }

            if ($request->soc_fecha && $request->soc_fecha < $preSocAprobada->soc_fecha) {
                $fechaPreSoc = \Carbon\Carbon::parse($preSocAprobada->soc_fecha)->format('d/m/Y');
                return back()->withErrors([
                    'soc_fecha' => "La fecha de la Socialización Final no puede ser anterior a la Pre-Socialización ({$fechaPreSoc})."
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

    public function programarFinal(Request $request)
    {
        $request->validate([
            'id_equipo'          => 'required|exists:equipo,equ_id',
            'fecha'              => 'required|date',
            'hora'               => 'nullable',
            'lugar'              => 'required|string|max:150',
            'entrega_cd_informe' => 'nullable|boolean',
        ], [
            'id_equipo.required' => 'El equipo es obligatorio.',
            'fecha.required'     => 'La fecha de la socialización final es obligatoria.',
            'lugar.required'     => 'El lugar del acto es obligatorio.',
        ]);

        $this->autorizarEquipoId($request->id_equipo);
        $equipo = Equipo::with('proyectoComunidad')->findOrFail($request->id_equipo);

        $tipoSocFinal = TipoSocializacion::where('tso_orden', 2)->first()
            ?? TipoSocializacion::where('tso_nombre', 'like', '%Final%')->first();

        if (!$tipoSocFinal) {
            return back()->withErrors(['error' => 'No se encontró el tipo de socialización final en tablas maestras.']);
        }

        $obsBase = trim((string) $request->observaciones);
        $obsBase = trim(str_replace(['[CD/INFORME ENTREGADO]', '[CD/INFORME PENDIENTE]'], '', $obsBase));
        $tagCd = $request->boolean('entrega_cd_informe') ? '[CD/INFORME ENTREGADO]' : '[CD/INFORME PENDIENTE]';
        $obsFinal = trim($tagCd . ($obsBase ? ' ' . $obsBase : ''));

        $soc = Socializacion::updateOrCreate(
            [
                'soc_id_equ' => $equipo->equ_id,
                'soc_id_tso' => $tipoSocFinal->tso_id,
            ],
            [
                'soc_id_tra'         => $equipo->equ_id_tra,
                'soc_fecha'          => $request->fecha,
                'soc_hora'           => $request->hora,
                'soc_lugar'          => $request->lugar,
                'soc_observaciones'  => $obsFinal,
                'soc_status'         => true,
                'soc_id_usu_created' => auth()->id(),
            ]
        );

        // Asegurar ProyectoComunidad, recorrido y PIN de defensa
        $proyecto = $equipo->proyectoComunidad;
        if (!$proyecto) {
            $proyecto = \App\Models\ProyectoComunidad::create([
                'pco_id_equ'              => $equipo->equ_id,
                'pco_id_com'              => \App\Models\Comunidad::first()?->com_id ?: 1,
                'pco_id_tpr'              => \App\Models\TipoProyecto::first()?->tpr_id ?: 1,
                'pco_id_mpr'              => \App\Models\ModalidadProyecto::first()?->mpr_id ?: 1,
                'pco_resumen'             => 'Proyecto Sociotecnológico - ' . ($equipo->equ_titulo ?: $equipo->equ_codigo),
                'pco_linea_investigacion' => 'Sistemas de Información y Desarrollo de Software',
                'pco_fecha_limite'        => now()->addMonths(6)->toDateString(),
                'pco_fecha_aprobacion'    => now()->toDateString(),
                'pin_defensa'             => str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
                'pco_status'              => true,
            ]);

            app(\App\Services\EvaluacionTrayectoService::class)->inicializarRecorridoProyecto(
                (int) $proyecto->pco_id,
                (int) $equipo->equ_id,
                (int) ($equipo->equ_id_tra ?: 1)
            );
        } elseif (empty($proyecto->pin_defensa)) {
            $pin = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $proyecto->update(['pin_defensa' => $pin]);
            \Illuminate\Support\Facades\DB::table('recorrido_evaluacion_semestral')
                ->where('id_proyecto', $proyecto->pco_id)
                ->update(['pin_defensa' => $pin]);
        }

        return back()->with('success', 'Fecha y lugar de la Socialización Final guardados exitosamente. El módulo de evaluación y el PIN de defensa han sido habilitados.');
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

        // Bloquear si ya fue evaluada
        $yaEvaluada = ($soc->tipoSocializacion?->tso_orden === 1 && $soc->soc_apto !== null)
            || ($soc->tipoSocializacion?->tso_orden !== 1 && $soc->soc_aprobado !== null);

        if ($yaEvaluada) {
            return back()->withErrors(['error' => 'Esta socialización ya cuenta con un resultado registrado y no puede ser modificada.']);
        }
        
        $soc->update([
            'soc_apto'                   => $request->soc_apto,
            'soc_aprobado'              => $request->soc_aprobado,
            'soc_observaciones_no_apto' => $request->soc_observaciones_no_apto,
            'soc_observaciones'         => $request->soc_observaciones,
            'soc_id_usu_updated'        => auth()->id(),
        ]);

        // Actualizar asistencia y avance de integrantes
        foreach ($request->integrantes ?? [] as $int) {
            $asistio = isset($int['asistio']) ? (bool)$int['asistio'] : true;
            // Regla UPTP: si no asiste a la pre-socialización o socialización, reprueba/no avanza automáticamente
            $avanza = $asistio ? (bool)($int['avanza'] ?? true) : false;

            IntegranteSocializacion::updateOrCreate(
                ['iso_id_soc' => $soc->soc_id, 'iso_id_usu' => $int['usu_id']],
                [
                    'iso_asistio'       => $asistio,
                    'iso_avanza'        => $avanza,
                    'iso_justificacion' => $int['justificacion'] ?? ($int['observaciones'] ?? null),
                    'iso_id_usu_created' => auth()->id(),
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

        // Notificar al líder del equipo solo si se registró un resultado explícito
        $lider = \App\Models\EquipoIntegrante::where('ein_id_equ', $soc->soc_id_equ)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();

        if ($lider) {
            $msg = '';
            if ($soc->tipoSocializacion?->tso_orden === 1) {
                if ($request->soc_apto !== null) {
                    $msg = $request->soc_apto 
                        ? "Tu equipo fue calificado como APTO en la Pre-Socialización." 
                        : "Tu equipo fue calificado como NO APTO en la Pre-Socialización.";
                }
            } else {
                if ($request->soc_aprobado !== null) {
                    $msg = $request->soc_aprobado 
                        ? "¡Felicitaciones! El proyecto de tu equipo fue APROBADO en la Socialización Final." 
                        : "El proyecto de tu equipo fue calificado como NO APROBADO en la Socialización Final.";
                }
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