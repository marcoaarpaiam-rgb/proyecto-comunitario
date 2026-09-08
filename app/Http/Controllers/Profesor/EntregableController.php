<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Entregable;
use App\Models\Equipo;
use App\Models\TipoEntregableTrayecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EntregableController extends Controller
{
    public function index()
    {
        $profesor = auth()->user();
        $secciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->pluck('pse_id_sec');

        $equipos = Equipo::whereIn('equ_id_sec', $secciones)
            ->where('equ_status', true)
            ->pluck('equ_id');

        return Inertia::render('Profesor/Entregables', [
            'entregables' => Entregable::whereIn('entr_id_equ', $equipos)
                ->with(['equipo', 'tipoEntregable'])
                ->orderBy('entr_fecha_subida', 'desc')
                ->get(),
        ]);
    }

    public function aprobar(Request $request, $id)
    {
        $entregable = Entregable::findOrFail($id);
        $entregable->update([
            'entr_aprobado'          => true,
            'entr_fecha_aprobacion'  => now(),
            'entr_id_usu_aprobado'   => auth()->id(),
            'entr_observacion_rechazo' => null,
        ]);
        return back()->with('success', 'Entregable aprobado. El líder puede enviarlo al repositorio.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'observacion' => 'required|string',
        ], [
            'observacion.required' => 'Debe indicar el motivo del rechazo.',
        ]);

        $entregable = Entregable::findOrFail($id);
        $entregable->update([
            'entr_aprobado'            => false,
            'entr_fecha_aprobacion'    => now(),
            'entr_id_usu_aprobado'     => auth()->id(),
            'entr_observacion_rechazo' => $request->observacion,
        ]);
        return back()->with('success', 'Entregable rechazado. El líder fue notificado.');
    }
}