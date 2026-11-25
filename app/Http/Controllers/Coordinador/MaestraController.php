<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\TipoProyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaestraController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/TiposProyecto', [
            'tipos' => TipoProyecto::orderBy('tpr_nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tpr_nombre' => 'required|string|max:100|unique:tipo_proyecto,tpr_nombre',
        ], [
            'tpr_nombre.required' => 'El nombre es obligatorio.',
            'tpr_nombre.unique'   => 'Ya existe una línea de investigación con ese nombre.',
        ]);

        $tipo = TipoProyecto::create([
            'tpr_nombre'        => $request->tpr_nombre,
            'tpr_status'        => true,
            'tpr_id_usu_created' => auth()->id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Línea de investigación creada correctamente.',
                'tipoProyecto' => $tipo,
            ]);
        }

        return back()->with('success', 'Línea de investigación creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoProyecto::findOrFail($id);

        $request->validate([
            'tpr_nombre' => "required|string|max:100|unique:tipo_proyecto,tpr_nombre,{$id},tpr_id",
        ], [
            'tpr_nombre.required' => 'El nombre es obligatorio.',
            'tpr_nombre.unique'   => 'Ya existe una línea de investigación con ese nombre.',
        ]);

        $tipo->update([
            'tpr_nombre'        => $request->tpr_nombre,
            'tpr_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Línea de investigación actualizada.');
    }

    public function destroy($id)
    {
        $tipo = TipoProyecto::findOrFail($id);
        $tipo->update(['tpr_status' => false, 'tpr_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Línea de investigación desactivada.');
    }

    /**
     * Permite crear una nueva opción rápida para cualquier catálogo (Línea, Modalidad, Organización, Encuentro)
     * cuando el usuario selecciona "Otro" en cualquier formulario.
     */
    public function crearOpcionRapida(Request $request)
    {
        $request->validate([
            'catalogo' => 'required|in:linea_investigacion,modalidad,tipo_organizacion,tipo_encuentro',
            'nombre'   => 'required|string|max:150',
        ], [
            'catalogo.required' => 'El tipo de catálogo es obligatorio.',
            'nombre.required'   => 'El nombre de la nueva opción es obligatorio.',
        ]);

        $catalogo = $request->catalogo;
        $nombre   = trim($request->nombre);
        $userId   = auth()->id();

        switch ($catalogo) {
            case 'linea_investigacion':
                $item = \App\Models\TipoProyecto::firstOrCreate(
                    ['tpr_nombre' => $nombre],
                    ['tpr_status' => true, 'tpr_id_usu_created' => $userId]
                );
                return response()->json([
                    'success' => true,
                    'id'      => $item->tpr_id,
                    'nombre'  => $item->tpr_nombre,
                    'message' => 'Línea de investigación agregada exitosamente.'
                ]);

            case 'modalidad':
                $item = \App\Models\ModalidadProyecto::firstOrCreate(
                    ['mpr_nombre' => $nombre],
                    ['mpr_status' => true, 'mpr_id_usu_created' => $userId]
                );
                return response()->json([
                    'success' => true,
                    'id'      => $item->mpr_id,
                    'nombre'  => $item->mpr_nombre,
                    'message' => 'Modalidad agregada exitosamente.'
                ]);

            case 'tipo_organizacion':
                $item = \App\Models\TipoOrganizacion::firstOrCreate(
                    ['tor_nombre' => $nombre],
                    ['tor_status' => true, 'tor_id_usu_created' => $userId]
                );
                return response()->json([
                    'success' => true,
                    'id'      => $item->tor_id,
                    'nombre'  => $item->tor_nombre,
                    'message' => 'Tipo de organización agregado exitosamente.'
                ]);

            case 'tipo_encuentro':
                $item = \App\Models\TipoEncuentro::firstOrCreate(
                    ['tep_nombre' => $nombre],
                    ['tep_activo' => true]
                );
                return response()->json([
                    'success' => true,
                    'id'      => $item->tep_id,
                    'nombre'  => $item->tep_nombre,
                    'message' => 'Tipo de encuentro agregado exitosamente.'
                ]);
        }

        return response()->json(['success' => false, 'message' => 'Catálogo no válido.'], 400);
    }
}