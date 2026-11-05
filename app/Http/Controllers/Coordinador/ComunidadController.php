<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Comunidad;
use App\Models\TipoOrganizacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComunidadController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Comunidades', [
            'comunidades' => \App\Models\Comunidad::where('com_status', true)
                ->withCount(['proyectosComunidad as total_equipos' => fn($q) =>
                    $q->whereHas('equipo', fn($q2) => $q2->where('equ_status', true))
                ])
                ->with(['proyectosComunidad.equipo' => fn($q) =>
                    $q->where('equ_status', true)->select('equ_id','equ_codigo','equ_titulo','equ_id_sec')
                ])
                ->orderBy('com_nombre')
                ->get(),
            'tiposOrganizacion' => TipoOrganizacion::where('tor_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'com_nombre'    => 'required|string|max:150|unique:comunidad,com_nombre',
            'com_ubicacion' => 'nullable|string|max:255',
            'com_id_tor'    => 'required|exists:tipo_organizacion,tor_id',
        ], [
            'com_nombre.required'    => 'El nombre es obligatorio.',
            'com_nombre.unique'      => 'Ya existe una comunidad con ese nombre.',
            'com_id_tor.required'    => 'El tipo de organización es obligatorio.',
        ]);

        $comunidad = Comunidad::create([
            'com_nombre'         => $request->com_nombre,
            'com_ubicacion'      => $request->com_ubicacion ?: 'Sin ubicación registrada',
            'com_id_tor'         => $request->com_id_tor,
            'com_status'         => true,
            'com_id_usu_created' => auth()->id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'comunidad' => $comunidad->load('tipoOrganizacion'),
            ]);
        }

        return back()->with('success', 'Comunidad registrada correctamente.')->with('nueva_comunidad_id', $comunidad->com_id);
    }

    public function update(Request $request, $id)
    {
        $comunidad = Comunidad::findOrFail($id);

        $request->validate([
            'com_nombre'    => "required|string|max:150|unique:comunidad,com_nombre,{$id},com_id",
            'com_ubicacion' => 'required|string|max:255',
            'com_id_tor'    => 'required|exists:tipo_organizacion,tor_id',
        ]);

        $comunidad->update([
            'com_nombre'         => $request->com_nombre,
            'com_ubicacion'      => $request->com_ubicacion,
            'com_id_tor'         => $request->com_id_tor,
            'com_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Comunidad actualizada correctamente.');
    }

    public function destroy($id)
    {
        $comunidad = Comunidad::findOrFail($id);
        $comunidad->update([
            'com_status'         => false,
            'com_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Comunidad desactivada.');
    }
}