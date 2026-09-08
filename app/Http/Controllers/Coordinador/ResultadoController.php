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
        return Inertia::render('Coordinador/Resultados', [
            'resultados' => ResultadoProyecto::with([
                'proyectoComunidad.equipo',
                'proyectoComunidad.comunidad',
                'equipo.seccion',
                'trayecto',
                'estadoProyecto',
            ])->get(),
            'proyectos' => ProyectoComunidad::with(['equipo','comunidad'])
                ->where('pco_status', true)
                ->get(),
            'estados'   => EstadoProyecto::where('epr_status', true)->get(),
            'trayectos' => Trayecto::where('tra_status', true)->get(),
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

        ResultadoProyecto::create([
            'rpr_id_pco'            => $request->rpr_id_pco,
            'rpr_id_equ'            => $proyecto->pco_id_equ,
            'rpr_id_tra'            => $request->rpr_id_tra,
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