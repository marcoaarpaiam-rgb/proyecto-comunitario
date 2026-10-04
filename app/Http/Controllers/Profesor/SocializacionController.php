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

class SocializacionController extends Controller
{
    public function index()
    {
        $profesor = auth()->user();
        $secciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->pluck('pse_id_sec');

        $equipos = Equipo::whereIn('equ_id_sec', $secciones)
            ->where('equ_status', true)
            ->with(['trayecto', 'seccion', 'integrantes.usuario'])
            ->get();

        return Inertia::render('Profesor/Socializaciones', [
            'socializaciones' => Socializacion::whereIn('soc_id_equ', $equipos->pluck('equ_id'))
                ->with([
                    'equipo', 'trayecto', 'tipoSocializacion',
                    'jurados', 'integrantes.usuario',
                    'socializacionPrevia',
                ])
                ->orderBy('soc_fecha', 'desc')
                ->get(),
            'equipos'          => $equipos,
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

        // Obtener trayecto del equipo
        $equipo = Equipo::findOrFail($request->soc_id_equ);

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

        $soc = Socializacion::findOrFail($id);
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

        return back()->with('success', 'Resultado registrado correctamente.');
    }
}