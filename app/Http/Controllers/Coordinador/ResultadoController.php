<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\ResultadoProyecto;
use App\Models\ProyectoComunidad;
use App\Models\EstadoProyecto;
use App\Models\Equipo;
use App\Models\Trayecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResultadoController extends Controller
{
    public function index()
    {
        $secciones = \App\Models\Seccion::where('sec_status', true)
            ->with(['trayecto', 'turno'])
            ->orderBy('sec_codigo')
            ->get();

        return Inertia::render('Coordinador/Resultados', [
            'resultados' => ResultadoProyecto::with([
                'proyectoComunidad.equipo.seccion.trayecto',
                'proyectoComunidad.equipo.seccion.turno',
                'proyectoComunidad.comunidad',
                'equipo.seccion.trayecto',
                'equipo.seccion.turno',
                'trayecto',
                'estadoProyecto',
            ])->orderBy('rpr_fecha_registro', 'desc')->get(),
            'proyectos' => ProyectoComunidad::with(['equipo','comunidad'])
                ->where('pco_status', true)
                ->get(),
            'estados'   => EstadoProyecto::where('epr_status', true)->get(),
            'trayectos' => Trayecto::where('tra_status', true)->get(),
            'secciones' => $secciones,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rpr_id_pco'         => 'required|exists:proyecto_comunidad,pco_id',
            'rpr_id_epr'         => 'required|exists:estado_proyecto,epr_id',
            'rpr_id_tra'         => 'required|exists:trayecto,tra_id',
            'rpr_fecha_registro' => 'required|date',
            'rpr_observaciones'  => 'nullable|string',
        ], [
            'rpr_id_pco.required'         => 'El proyecto es obligatorio.',
            'rpr_id_epr.required'         => 'El estado es obligatorio.',
            'rpr_id_tra.required'         => 'El trayecto es obligatorio.',
            'rpr_fecha_registro.required' => 'La fecha es obligatoria.',
        ]);

        $proyecto = ProyectoComunidad::findOrFail($request->rpr_id_pco);

        $yaExiste = ResultadoProyecto::where('rpr_id_pco', $request->rpr_id_pco)->exists();
        if ($yaExiste) {
            return back()->withErrors(['rpr_id_pco' => 'Este proyecto ya tiene un resultado registrado.']);
        }
        
        // Verificar que existe socialización final aprobada
        $socFinalAprobada = \App\Models\Socializacion::where('soc_id_equ', $proyecto->pco_id_equ)
            ->whereHas('tipoSocializacion', fn($q) => $q->where('tso_orden', 2))
            ->where('soc_aprobado', true)
            ->exists();

        if (!$socFinalAprobada) {
            return back()->withErrors([
                'rpr_id_pco' => 'No se puede registrar el resultado sin una Socialización Final aprobada para este equipo.'
            ]);
        }

        ResultadoProyecto::create([
            'rpr_id_pco'            => $request->rpr_id_pco,
            'rpr_id_tra'            => $request->rpr_id_tra,
            'rpr_id_equ' => $proyecto->pco_id_equ,
            'rpr_id_epr'            => $request->rpr_id_epr,
            'rpr_habilitado_tardio' => $request->rpr_habilitado_tardio ?? false,
            'rpr_observaciones'     => $request->rpr_observaciones,
            'rpr_fecha_registro'    => $request->rpr_fecha_registro,
            'rpr_status'            => true,
            'rpr_id_usu_created'    => auth()->id(),
        ]);

        return back()->with('success', 'Resultado registrado correctamente.');
    }

    public function habilitarTardio($id)
    {
        $resultado = ResultadoProyecto::findOrFail($id);
        $resultado->update([
            'rpr_habilitado_tardio' => true,
            'rpr_id_usu_updated'    => auth()->id(),
        ]);
        return back()->with('success', 'Entrega tardía habilitada para este equipo.');
    }
}