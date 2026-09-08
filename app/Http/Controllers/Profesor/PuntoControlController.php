<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\PuntoControl;
use App\Models\Equipo;
use App\Models\Trayecto;
use App\Models\SeguimientoEquipo;
use App\Models\AsistenciaPuntoControl;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PuntoControlController extends Controller
{
    public function index()
    {
        $profesor = auth()->user();

        $secciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->pluck('pse_id_sec');

        $equipos = Equipo::whereIn('equ_id_sec', $secciones)
            ->where('equ_status', true)
            ->with(['trayecto','seccion','integrantes.usuario'])
            ->get();

        return Inertia::render('Profesor/PuntosControl', [
            'puntosControl' => PuntoControl::whereIn('puc_id_equ', $equipos->pluck('equ_id'))
                ->where('puc_status', true)
                ->with(['equipo','trayecto'])
                ->orderBy('puc_fecha_limite')
                ->get(),
            'equipos'   => $equipos,
            'trayectos' => Trayecto::where('tra_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'puc_id_equ'      => 'required|exists:equipo,equ_id',
            'puc_id_tra'      => 'required|exists:trayecto,tra_id',
            'puc_nombre'      => 'required|string|max:150',
            'puc_descripcion' => 'nullable|string',
            'puc_fecha_limite'=> 'required|date',
            'puc_orden'       => 'required|integer|min:1',
            'puc_dias_aviso'  => 'required|integer|min:1|max:30',
        ], [
            'puc_id_equ.required'       => 'El equipo es obligatorio.',
            'puc_id_tra.required'       => 'El trayecto es obligatorio.',
            'puc_nombre.required'       => 'El nombre es obligatorio.',
            'puc_fecha_limite.required' => 'La fecha límite es obligatoria.',
            'puc_orden.required'        => 'El orden es obligatorio.',
        ]);

        PuntoControl::create([
            'puc_id_equ'          => $request->puc_id_equ,
            'puc_id_tra'          => $request->puc_id_tra,
            'puc_nombre'          => $request->puc_nombre,
            'puc_descripcion'     => $request->puc_descripcion,
            'puc_fecha_limite'    => $request->puc_fecha_limite,
            'puc_orden'           => $request->puc_orden,
            'puc_dias_aviso'      => $request->puc_dias_aviso ?? 7,
            'puc_es_entregable'   => $request->puc_es_entregable ?? false,
            'puc_status'          => true,
            'puc_id_usu_created'  => auth()->id(),
        ]);

        return back()->with('success', 'Punto de control creado correctamente.');
    }

    public function registrarSeguimiento(Request $request, $id)
    {
        $request->validate([
            'seq_cumplido'      => 'required|boolean',
            'seq_observaciones' => 'nullable|string',
            'asistencias'       => 'required|array',
            'asistencias.*.usu_id'   => 'required|exists:usuario,usu_id',
            'asistencias.*.asistio'  => 'required|boolean',
        ]);

        $punto = PuntoControl::findOrFail($id);

        $seq = SeguimientoEquipo::updateOrCreate(
            ['seq_id_equ' => $punto->puc_id_equ, 'seq_id_puc' => $punto->puc_id],
            [
                'seq_cumplido'           => $request->seq_cumplido,
                'seq_fecha_cumplimiento' => $request->seq_cumplido ? now()->toDateString() : null,
                'seq_cumplido_a_tiempo'  => $request->seq_cumplido
                    ? (now()->toDateString() <= $punto->puc_fecha_limite)
                    : null,
                'seq_observaciones'      => $request->seq_observaciones,
                'seq_fecha_registro'     => now()->toDateString(),
                'seq_id_usu_updated'     => auth()->id(),
            ]
        );

        foreach ($request->asistencias as $asistencia) {
            AsistenciaPuntoControl::updateOrCreate(
                ['apc_id_seq' => $seq->seq_id, 'apc_id_usu' => $asistencia['usu_id']],
                [
                    'apc_asistio'        => $asistencia['asistio'],
                    'apc_observaciones'  => $asistencia['observacion'] ?? null,
                    'apc_id_usu_created' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Seguimiento registrado correctamente.');
    }

    public function reprogramar(Request $request, $id)
    {
        $request->validate([
            'puc_fecha_reprogramada'    => 'required|date|after:today',
            'puc_motivo_reprogramacion' => 'required|string',
        ]);

        $punto = PuntoControl::findOrFail($id);
        $punto->update([
            'puc_fecha_reprogramada'    => $request->puc_fecha_reprogramada,
            'puc_motivo_reprogramacion' => $request->puc_motivo_reprogramacion,
            'puc_id_usu_updated'        => auth()->id(),
        ]);

        return back()->with('success', 'Punto de control reprogramado.');
    }

    public function destroy($id)
    {
        $punto = PuntoControl::findOrFail($id);
        $punto->update([
            'puc_status'         => false,
            'puc_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Punto de control eliminado.');
    }
}