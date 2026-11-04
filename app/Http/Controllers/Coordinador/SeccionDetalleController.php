<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Equipo;
use Inertia\Inertia;
use App\Models\Usuario; 
use App\Models\UsuarioRol; 
use App\Models\ProfesorSeccion; 
use Illuminate\Http\Request;

class SeccionDetalleController extends Controller
{
    public function show($id)
    {
        $seccion = Seccion::with(['trayecto','turno'])->findOrFail($id);
        $equipos = Equipo::where('equ_id_sec', $id)
            ->where('equ_status', true)
            ->with([
                'integrantes.usuario',
                'proyectoComunidad.comunidad',
                'proyectoComunidad.tipoProyecto',
                'resultadoProyecto.estadoProyecto',
                'trayecto',
            ])->get();

        return Inertia::render('Coordinador/SeccionDetalle', [
            'seccion' => $seccion,
            'equipos' => $equipos,
            'profesorActual' => Usuario::whereHas('seccionesProfesor', fn($q) => $q->where('pse_id_sec', $id)->where('pse_status', true))
                ->first(['usu_id', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_cedula']),
            'profesores' => Usuario::where('usu_status', true)
                ->whereHas('usuarioRoles', fn($q) => $q->where('uro_status', true)
                    ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'profesor_proyecto')))
                ->orderBy('usu_primer_apellido')
                ->get(['usu_id', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_cedula']),
        ]);
    }

    public function asignarProfesor(Request $request, $id)
    {
        $request->validate(['pse_id_usu' => 'required|exists:usuario,usu_id']);
        $seccion = Seccion::findOrFail($id);

        $esProfesor = UsuarioRol::where('uro_id_usu', $request->pse_id_usu)->where('uro_status', true)
            ->whereHas('rol', fn($q) => $q->where('rol_nombre', 'profesor_proyecto'))->exists();
        if (!$esProfesor) {
            return back()->withErrors(['pse_id_usu' => 'El usuario seleccionado no es profesor de proyecto.']);
        }

        \DB::transaction(function () use ($seccion, $request) {
            ProfesorSeccion::where('pse_id_sec', $seccion->sec_id)->where('pse_status', true)
                ->update(['pse_status' => false, 'pse_fecha_fin' => now()->toDateString()]);

            ProfesorSeccion::create([
                'pse_id_usu'         => $request->pse_id_usu,
                'pse_id_sec'         => $seccion->sec_id,
                'pse_fecha_asignacion' => now()->toDateString(),
                'pse_status'         => true,
                'pse_id_usu_created' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Profesor asignado a la sección.');
    }
}