<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Trayecto;
use App\Models\Turno;
use App\Models\Usuario;
use App\Models\ProfesorSeccion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeccionController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Secciones', [
            'secciones' => Seccion::with(['trayecto', 'turno', 'profesorActual.usuario'])
                ->where('sec_status', true)
                ->orderBy('sec_codigo')
                ->get(),
            'trayectos' => Trayecto::where('tra_status', true)->get(),
            'turnos'    => Turno::where('tur_status', true)->get(),
            'profesores'=> Usuario::where('usu_status', true)
                ->whereHas('usuarioRoles', fn($q) => $q->where('uro_status', true)
                    ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'profesor_proyecto')))
                ->orderBy('usu_primer_apellido')
                ->get(['usu_id', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_cedula']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sec_codigo'   => 'required|string|max:20|unique:seccion,sec_codigo',
            'sec_id_tra'   => 'required|exists:trayecto,tra_id',
            'sec_semestre' => 'nullable|integer|min:1|max:8',
            'sec_id_tur'   => 'required|exists:turno,tur_id',
            'pse_id_usu'   => 'nullable|exists:usuario,usu_id',
        ], [
            'sec_codigo.required' => 'El código es obligatorio.',
            'sec_codigo.unique'   => 'Ya existe una sección con ese código.',
            'sec_id_tra.required' => 'El trayecto es obligatorio.',
            'sec_id_tur.required' => 'El turno es obligatorio.',
        ]);

        $seccion = Seccion::create([
            'sec_codigo'         => $request->sec_codigo,
            'sec_id_tra'         => $request->sec_id_tra,
            'sec_semestre'       => $request->sec_semestre ?? ($request->sec_id_tra ? ($request->sec_id_tra * 2 - 1) : 1),
            'sec_id_tur'         => $request->sec_id_tur,
            'sec_status'         => true,
            'sec_id_usu_created' => auth()->id(),
        ]);

        if ($request->pse_id_usu) {
            ProfesorSeccion::create([
                'pse_id_usu'           => $request->pse_id_usu,
                'pse_id_sec'           => $seccion->sec_id,
                'pse_fecha_asignacion' => now()->toDateString(),
                'pse_status'           => true,
                'pse_id_usu_created'   => auth()->id(),
            ]);
        }

        return back()->with('success', 'Sección creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $seccion = Seccion::findOrFail($id);

        $request->validate([
            'sec_codigo'   => "required|string|max:20|unique:seccion,sec_codigo,{$id},sec_id",
            'sec_id_tra'   => 'required|exists:trayecto,tra_id',
            'sec_semestre' => 'nullable|integer|min:1|max:8',
            'sec_id_tur'   => 'required|exists:turno,tur_id',
            'pse_id_usu'   => 'nullable|exists:usuario,usu_id',
        ]);

        $seccion->update([
            'sec_codigo'         => $request->sec_codigo,
            'sec_id_tra'         => $request->sec_id_tra,
            'sec_semestre'       => $request->sec_semestre ?? $seccion->sec_semestre,
            'sec_id_tur'         => $request->sec_id_tur,
            'sec_id_usu_updated' => auth()->id(),
        ]);

        if ($request->has('pse_id_usu')) {
            $nuevoProf = $request->pse_id_usu;
            $profActual = ProfesorSeccion::where('pse_id_sec', $seccion->sec_id)
                ->where('pse_status', true)
                ->first();

            if ($profActual && $profActual->pse_id_usu != $nuevoProf) {
                $profActual->update([
                    'pse_status'    => false,
                    'pse_fecha_fin' => now()->toDateString(),
                ]);
            }

            if ($nuevoProf && (!$profActual || $profActual->pse_id_usu != $nuevoProf)) {
                ProfesorSeccion::create([
                    'pse_id_usu'           => $nuevoProf,
                    'pse_id_sec'           => $seccion->sec_id,
                    'pse_fecha_asignacion' => now()->toDateString(),
                    'pse_status'           => true,
                    'pse_id_usu_created'   => auth()->id(),
                ]);
            }
        }

        return back()->with('success', 'Sección actualizada correctamente.');
    }

    public function destroy($id)
    {
        $seccion = Seccion::findOrFail($id);
        $seccion->update([
            'sec_status'         => false,
            'sec_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Sección desactivada.');
    }
}