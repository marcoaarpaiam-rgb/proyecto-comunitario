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

    private function notificarLider(Entregable $entregable, string $mensaje): void
    {
        $lider = $entregable->equipo?->integrantes()
            ->where('ein_es_lider', true)->where('ein_status', true)->first();

        if ($lider) {
            \App\Models\Notificacion::create([
                'not_id_usu'          => $lider->ein_id_usu,
                'not_id_tno'          => \App\Models\TipoNotificacion::first()?->tno_id,
                'not_id_equ'          => $entregable->entr_id_equ,
                'not_mensaje'         => $mensaje,
                'not_leida'           => false,
                'not_fecha_generacion'=> now(),
            ]);
        }
    }

    public function aprobar($id)
    {
        $entregable = Entregable::with('equipo.integrantes')->findOrFail($id);
        $entregable->update([
            'entr_aprobado'         => true,
            'entr_fecha_aprobacion' => now(),
            'entr_id_usu_aprobado'  => auth()->id(),
        ]);
        $this->notificarLider($entregable,
            "Tu entregable \"{$entregable->entr_nombre_archivo}\" fue APROBADO por el profesor.");
        return back()->with('success', 'Entregable aprobado. El líder fue notificado.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate(['observacion' => 'required|string']);
        $entregable = Entregable::with('equipo.integrantes')->findOrFail($id);
        $entregable->update([
            'entr_aprobado'            => false,
            'entr_fecha_aprobacion'    => now(),
            'entr_id_usu_aprobado'     => auth()->id(),
            'entr_observacion_rechazo' => $request->observacion,
        ]);
        $this->notificarLider($entregable,
            "Tu entregable \"{$entregable->entr_nombre_archivo}\" fue RECHAZADO. Motivo: {$request->observacion}");
        return back()->with('success', 'Entregable rechazado. El líder fue notificado.');
    }
}